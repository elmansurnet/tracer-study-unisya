<?php

namespace App\Services;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    private const PURPOSES = [
        'login',
        'employer_access',
        'phone_verify',
        'email_verify',
    ];

    public function generate(
        string $identifier,
        string $identifierType,
        string $purpose = 'login',
        ?string $referenceId = null,
        ?string $ipAddress = null
    ): string {
        $this->ensureValidPurpose($purpose);
        $this->ensureValidIdentifierType($identifierType);

        OtpVerification::where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

        $otpPlain = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'identifier'      => $identifier,
            'identifier_type' => $identifierType,
            'otp_code'        => Hash::make($otpPlain),
            'purpose'         => $purpose,
            'reference_id'    => $referenceId,
            'attempts'        => 0,
            'max_attempts'    => 5,
            'is_used'         => false,
            'expires_at'      => now()->addMinutes(5),
            'ip_address'      => $ipAddress,
        ]);

        return $otpPlain;
    }

    public function verify(
        string $identifier,
        string $otpPlain,
        string $purpose = 'login',
        ?string $referenceId = null
    ): bool {
        $this->ensureValidPurpose($purpose);

        $otp = OtpVerification::query()
            ->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->when($referenceId, fn ($query) => $query->where('reference_id', $referenceId))
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return false;
        }

        if ($otp->isMaxAttemptsReached()) {
            $otp->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

            return false;
        }

        $otp->increment('attempts');

        if (! Hash::check($otpPlain, $otp->otp_code)) {
            if ($otp->fresh()->isMaxAttemptsReached()) {
                $otp->update([
                    'is_used' => true,
                    'used_at' => now(),
                ]);
            }

            return false;
        }

        $otp->update([
            'is_used' => true,
            'used_at' => now(),
        ]);

        return true;
    }

    public function cleanupExpired(): int
    {
        return OtpVerification::query()
            ->where(function ($query) {
                $query->where('expires_at', '<', now())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('is_used', true)
                            ->where('updated_at', '<', now()->subHours(24));
                    });
            })
            ->delete();
    }

    private function ensureValidPurpose(string $purpose): void
    {
        if (! in_array($purpose, self::PURPOSES, true)) {
            throw ValidationException::withMessages([
                'purpose' => ['Purpose OTP tidak valid.'],
            ]);
        }
    }

    private function ensureValidIdentifierType(string $identifierType): void
    {
        if (! in_array($identifierType, ['email', 'whatsapp'], true)) {
            throw ValidationException::withMessages([
                'identifier_type' => ['Tipe identifier tidak valid.'],
            ]);
        }
    }
}