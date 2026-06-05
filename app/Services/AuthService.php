<?php

namespace App\Services;

use App\Models\EmployerAccessToken;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

class AuthService
{
    public function __construct(
        protected OtpService $otpService
    ) {
    }

    // =========================================================================
    // LOGIN — Email & Password
    // =========================================================================

    public function login(string $email, string $password, Request $request): array
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('Email atau password tidak valid.');
        }

        if (! $user->is_active) {
            throw new AuthenticationException('Akun Anda dinonaktifkan. Hubungi administrator.');
        }

        $token = $this->issueToken($user, $request, 'auth-login');

        return [
            'status'  => true,
            'message' => 'Login berhasil.',
            'data'    => $this->buildAuthenticatedUserResponse($user->fresh(), $token),
        ];
    }

    // =========================================================================
    // LOGIN — OTP (Alumni / Super Admin)
    // =========================================================================

    public function requestLoginOtp(
        string $identifier,
        string $identifierType = 'email',
        ?string $ipAddress = null
    ): array {
        $user = User::query()
            ->when($identifierType === 'email', fn ($q) => $q->where('email', $identifier))
            ->when($identifierType === 'whatsapp', fn ($q) => $q->where('phone', $identifier))
            ->first();

        if (! $user) {
            throw new AuthenticationException('Akun tidak ditemukan.');
        }

        if (! $user->is_active) {
            throw new AuthenticationException('Akun Anda dinonaktifkan. Hubungi administrator.');
        }

        $otp = $this->otpService->generate(
            identifier:     $identifier,
            identifierType: $identifierType,
            purpose:        'login',
            referenceId:    $user->id,
            ipAddress:      $ipAddress
        );

        return [
            'status'  => true,
            'message' => 'OTP berhasil dikirim.',
            'data'    => [
                'identifier'      => $identifier,
                'identifier_type' => $identifierType,
                'expires_in'      => 300,
                'otp_preview'     => app()->environment('local') ? $otp : null,
            ],
        ];
    }

    public function loginWithOtp(
        string $identifier,
        string $otpCode,
        Request $request,
        string $identifierType = 'email'
    ): array {
        $isValid = $this->otpService->verify(
            identifier: $identifier,
            otpPlain:   $otpCode,
            purpose:    'login'
        );

        if (! $isValid) {
            throw new AuthenticationException('OTP tidak valid atau sudah kadaluarsa.');
        }

        $user = User::query()
            ->when($identifierType === 'email', fn ($q) => $q->where('email', $identifier))
            ->when($identifierType === 'whatsapp', fn ($q) => $q->where('phone', $identifier))
            ->first();

        if (! $user) {
            throw new AuthenticationException('Akun tidak ditemukan.');
        }

        if (! $user->is_active) {
            throw new AuthenticationException('Akun Anda dinonaktifkan. Hubungi administrator.');
        }

        $token = $this->issueToken($user, $request, 'auth-otp');

        return [
            'status'  => true,
            'message' => 'Login OTP berhasil.',
            'data'    => $this->buildAuthenticatedUserResponse($user->fresh(), $token),
        ];
    }

    // =========================================================================
    // EMPLOYER OTP — Request & Verify
    // Dipanggil oleh EmployerAccessController
    // =========================================================================

    /**
     * Request OTP untuk employer berdasarkan plain access token.
     *
     * Flow (sesuai 04_ARCHITECTURE.md §4.4):
     *   1. Cari EmployerAccessToken berdasarkan hash dari plain token.
     *   2. Validasi: belum dipakai, belum dicabut, belum expired.
     *   3. Generate OTP dengan purpose 'employer_access', reference_id = token id.
     *   4. Kirim OTP via WA/Email ke kontak employer (via WhatsAppService/MailService).
     *   5. Return contact_masked dan expires_in.
     *
     * @throws AuthenticationException
     */
    public function requestEmployerOtp(
        string $plainToken,
        OtpService $otpService
    ): array {
        $hashed = hash('sha256', $plainToken);

        /** @var EmployerAccessToken|null $accessToken */
        $accessToken = EmployerAccessToken::query()
            ->where('token', $hashed)
            ->where('is_used', false)
            ->where('is_revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $accessToken) {
            throw new AuthenticationException(
                'Token akses employer tidak valid, sudah digunakan, atau kadaluarsa.'
            );
        }

        // Tentukan identifier & type untuk OTP (preferensi: whatsapp > email)
        $identifier     = $accessToken->employer_phone ?? $accessToken->employer_email;
        $identifierType = $accessToken->employer_phone ? 'whatsapp' : 'email';

        if (! $identifier) {
            throw new AuthenticationException(
                'Data kontak employer tidak tersedia pada token ini.'
            );
        }

        $otpPlain = $otpService->generate(
            identifier:     $identifier,
            identifierType: $identifierType,
            purpose:        'employer_access',
            referenceId:    $accessToken->id,
        );

        // TODO: kirim $otpPlain via WhatsAppService / MailService
        // Contoh: WhatsAppService::send($identifier, "Kode OTP Anda: {$otpPlain}");

        // Masking: tampilkan hanya 3 karakter awal dan 2 terakhir
        $contactMasked = $this->maskContact($identifier);

        return [
            'message'         => 'OTP berhasil dikirim ke kontak employer.',
            'contact_masked'  => $contactMasked,
            'expires_in'      => 300,
            'otp_preview'     => app()->environment('local') ? $otpPlain : null,
        ];
    }

    /**
     * Verifikasi OTP employer dan terbitkan Sanctum token dengan ability 'employer'.
     *
     * Flow (sesuai 07_SECURITY.md §2.1.2 & §2.1.4):
     *   1. Hash plain token → cari EmployerAccessToken.
     *   2. Verifikasi OTP dengan purpose 'employer_access'.
     *   3. Tandai access token sebagai is_used = true.
     *   4. Cari atau buat User sementara bertipe 'pengguna_alumni' jika belum ada.
     *   5. Terbitkan Sanctum token dengan ability ['employer'] dan expired 2 jam.
     *   6. Return access_token, employer_info, dan questionnaires terkait.
     *
     * @throws AuthenticationException
     */
    public function verifyEmployerOtp(
        string $plainToken,
        string $otpCode,
        OtpService $otpService,
        ?string $ipAddress = null
    ): array {
        $hashed = hash('sha256', $plainToken);

        /** @var EmployerAccessToken|null $accessToken */
        $accessToken = EmployerAccessToken::query()
            ->where('token', $hashed)
            ->where('is_used', false)
            ->where('is_revoked', false)
            ->where('expires_at', '>', now())
            ->with(['alumni.studyProgram.faculty'])
            ->first();

        if (! $accessToken) {
            throw new AuthenticationException(
                'Token akses employer tidak valid, sudah digunakan, atau kadaluarsa.'
            );
        }

        $identifier     = $accessToken->employer_phone ?? $accessToken->employer_email;
        $identifierType = $accessToken->employer_phone ? 'whatsapp' : 'email';

        $isValid = $otpService->verify(
            identifier:  $identifier,
            otpPlain:    $otpCode,
            purpose:     'employer_access',
            referenceId: $accessToken->id,
        );

        if (! $isValid) {
            throw new AuthenticationException('OTP tidak valid atau sudah kadaluarsa.');
        }

        // Tandai access token sudah digunakan
        $accessToken->update([
            'is_used'    => true,
            'used_at'    => now(),
            'used_ip'    => $ipAddress,
        ]);

        // Cari atau buat User employer (role: pengguna_alumni)
        $employerUser = User::query()
            ->where('email', $accessToken->employer_email)
            ->first();

        if (! $employerUser) {
            $employerUser = User::create([
                'name'      => $accessToken->employer_name ?? 'Employer',
                'email'     => $accessToken->employer_email,
                'password'  => Hash::make(\Illuminate\Support\Str::random(32)),
                'role'      => 'pengguna_alumni',
                'is_active' => true,
            ]);
        }

        // Terbitkan Sanctum token scope 'employer', expired 2 jam
        $sanctumToken = $employerUser->createToken(
            'employer-token',
            ['employer'],
            now()->addHours(2)
        );

        // Catat last_login
        $employerUser->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ])->save();

        return [
            'access_token' => $sanctumToken->plainTextToken,
            'token_type'   => 'Bearer',
            'expires_in'   => 7200,
            'employer_info' => [
                'name'           => $accessToken->employer_name,
                'company'        => $accessToken->employer_company,
                'email'          => $accessToken->employer_email,
                'phone'          => $accessToken->employer_phone,
            ],
            'alumni' => $accessToken->alumni ? [
                'id'            => $accessToken->alumni->id,
                'nim'           => $accessToken->alumni->nim,
                'name'          => $accessToken->alumni->name,
                'study_program' => $accessToken->alumni->studyProgram ? [
                    'id'      => $accessToken->alumni->studyProgram->id,
                    'name'    => $accessToken->alumni->studyProgram->name,
                    'faculty' => $accessToken->alumni->studyProgram->faculty ? [
                        'id'   => $accessToken->alumni->studyProgram->faculty->id,
                        'name' => $accessToken->alumni->studyProgram->faculty->name,
                    ] : null,
                ] : null,
            ] : null,
        ];
    }

    // =========================================================================
    // ME & LOGOUT
    // =========================================================================

    public function me(User $user): array
    {
        return [
            'status'  => true,
            'message' => 'Profil pengguna berhasil diambil.',
            'data'    => $this->buildAuthenticatedUserResponse($user->fresh()),
        ];
    }

    public function logout(User $user): array
    {
        $user->currentAccessToken()?->delete();

        return [
            'status'  => true,
            'message' => 'Logout berhasil.',
            'data'    => null,
        ];
    }

    // =========================================================================
    // INTERNAL HELPERS
    // =========================================================================

    protected function issueToken(User $user, Request $request, string $tokenName): NewAccessToken
    {
        $abilities = match ($user->role) {
            'super_admin' => ['admin'],
            'alumni'      => ['alumni'],
            default       => [],
        };

        $token = $user->createToken($tokenName, $abilities);

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        return $token;
    }

    protected function buildAuthenticatedUserResponse(User $user, ?NewAccessToken $token = null): array
    {
        $user->loadMissing('alumni.studyProgram.faculty');

        return [
            'token'       => $token?->plainTextToken,
            'token_type'  => $token ? 'Bearer' : null,
            'permissions' => $this->resolvePermissions($user),
            'user'        => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'phone'             => $user->phone,
                'role'              => $user->role,
                'role_label'        => match ($user->role) {
                    'super_admin' => 'Super Admin',
                    'alumni'      => 'Alumni',
                    default       => 'Pengguna',
                },
                'is_active'          => (bool) $user->is_active,
                'email_verified_at'  => optional($user->email_verified_at)?->toISOString(),
                'phone_verified_at'  => optional($user->phone_verified_at)?->toISOString(),
                'last_login_at'      => optional($user->last_login_at)?->toISOString(),
            ],
            'alumni' => $user->alumni ? [
                'id'           => $user->alumni->id,
                'nim'          => $user->alumni->nim,
                'name'         => $user->alumni->name,
                'study_program' => $user->alumni->studyProgram ? [
                    'id'     => $user->alumni->studyProgram->id,
                    'name'   => $user->alumni->studyProgram->name,
                    'faculty' => $user->alumni->studyProgram->faculty ? [
                        'id'   => $user->alumni->studyProgram->faculty->id,
                        'name' => $user->alumni->studyProgram->faculty->name,
                    ] : null,
                ] : null,
            ] : null,
        ];
    }

    protected function resolvePermissions(User $user): array
    {
        return match ($user->role) {
            'super_admin' => [
                'access_admin_dashboard',
                'manage_users',
                'manage_master_data',
                'manage_questionnaires',
                'manage_tracer_studies',
                'view_reports',
                'manage_settings',
                'view_audit_logs',
            ],
            'alumni' => [
                'access_alumni_dashboard',
                'update_own_profile',
                'manage_own_employment',
                'submit_requests',
                'fill_questionnaire',
                'manage_employer_tokens',
            ],
            default => [],
        };
    }

    /**
     * Masking kontak: tampilkan 3 karakter awal + "****" + 2 karakter terakhir.
     * Contoh: "081234567890" → "081****90"
     */
    private function maskContact(string $contact): string
    {
        $length = mb_strlen($contact);

        if ($length <= 5) {
            return str_repeat('*', $length);
        }

        return mb_substr($contact, 0, 3)
            . '****'
            . mb_substr($contact, -2);
    }
}