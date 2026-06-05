<?php

namespace App\Services;

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
            'status' => true,
            'message' => 'Login berhasil.',
            'data' => $this->buildAuthenticatedUserResponse($user->fresh(), $token),
        ];
    }

    public function requestLoginOtp(
        string $identifier,
        string $identifierType = 'email',
        ?string $ipAddress = null
    ): array {
        $user = User::query()
            ->when($identifierType === 'email', fn ($query) => $query->where('email', $identifier))
            ->when($identifierType === 'whatsapp', fn ($query) => $query->where('phone', $identifier))
            ->first();

        if (! $user) {
            throw new AuthenticationException('Akun tidak ditemukan.');
        }

        if (! $user->is_active) {
            throw new AuthenticationException('Akun Anda dinonaktifkan. Hubungi administrator.');
        }

        $otp = $this->otpService->generate(
            identifier: $identifier,
            identifierType: $identifierType,
            purpose: 'login',
            referenceId: $user->id,
            ipAddress: $ipAddress
        );

        return [
            'status' => true,
            'message' => 'OTP berhasil dikirim.',
            'data' => [
                'identifier' => $identifier,
                'identifier_type' => $identifierType,
                'expires_in' => 300,
                'otp_preview' => app()->environment('local') ? $otp : null,
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
            otpPlain: $otpCode,
            purpose: 'login'
        );

        if (! $isValid) {
            throw new AuthenticationException('OTP tidak valid atau sudah kadaluarsa.');
        }

        $user = User::query()
            ->when($identifierType === 'email', fn ($query) => $query->where('email', $identifier))
            ->when($identifierType === 'whatsapp', fn ($query) => $query->where('phone', $identifier))
            ->first();

        if (! $user) {
            throw new AuthenticationException('Akun tidak ditemukan.');
        }

        if (! $user->is_active) {
            throw new AuthenticationException('Akun Anda dinonaktifkan. Hubungi administrator.');
        }

        $token = $this->issueToken($user, $request, 'auth-otp');

        return [
            'status' => true,
            'message' => 'Login OTP berhasil.',
            'data' => $this->buildAuthenticatedUserResponse($user->fresh(), $token),
        ];
    }

    public function me(User $user): array
    {
        return [
            'status' => true,
            'message' => 'Profil pengguna berhasil diambil.',
            'data' => $this->buildAuthenticatedUserResponse($user->fresh()),
        ];
    }

    public function logout(User $user): array
    {
        $user->currentAccessToken()?->delete();

        return [
            'status' => true,
            'message' => 'Logout berhasil.',
            'data' => null,
        ];
    }

    protected function issueToken(User $user, Request $request, string $tokenName): NewAccessToken
    {
        $abilities = match ($user->role) {
            'super_admin' => ['admin'],
            'alumni' => ['alumni'],
            default => [],
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
            'token' => $token?->plainTextToken,
            'token_type' => $token ? 'Bearer' : null,
            'permissions' => $this->resolvePermissions($user),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'role_label' => $user->role === 'super_admin' ? 'Super Admin' : 'Alumni',
                'is_active' => (bool) $user->is_active,
                'email_verified_at' => optional($user->email_verified_at)?->toISOString(),
                'phone_verified_at' => optional($user->phone_verified_at)?->toISOString(),
                'last_login_at' => optional($user->last_login_at)?->toISOString(),
            ],
            'alumni' => $user->alumni ? [
                'id' => $user->alumni->id,
                'nim' => $user->alumni->nim,
                'name' => $user->alumni->name,
                'study_program' => $user->alumni->studyProgram ? [
                    'id' => $user->alumni->studyProgram->id,
                    'name' => $user->alumni->studyProgram->name,
                    'faculty' => $user->alumni->studyProgram->faculty ? [
                        'id' => $user->alumni->studyProgram->faculty->id,
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
}