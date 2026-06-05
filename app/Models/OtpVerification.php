<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasUuids;

    /**
     * Mass-assignable fields.
     *
     * 'reference_id' dan 'ip_address' WAJIB ada di sini karena
     * OtpService::generate() mengirim kedua field tersebut ke ::create().
     * Tanpa ini, kedua field akan diabaikan secara silent (mass assignment protection).
     */
    protected $fillable = [
        'identifier',
        'identifier_type',
        'otp_code',
        'purpose',
        'reference_id',
        'ip_address',
        'attempts',
        'max_attempts',
        'is_used',
        'used_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used'    => 'boolean',
            'used_at'    => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isMaxAttemptsReached(): bool
    {
        return $this->attempts >= $this->max_attempts;
    }
}