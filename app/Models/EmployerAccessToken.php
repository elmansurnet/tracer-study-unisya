<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployerAccessToken extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'alumni_id', 'institution_id',
        'contact_name', 'contact_phone', 'contact_email',
        'token', 'token_plain',
        'is_used', 'is_revoked', 'otp_verified',
        'expires_at', 'used_at', 'revoked_at',
        'tracer_study_id',
        'created_by', 'revoked_by',
    ];

    protected $hidden = ['token', 'token_plain'];

    protected function casts(): array
    {
        return [
            'is_used'      => 'boolean',
            'is_revoked'   => 'boolean',
            'otp_verified' => 'boolean',
            'expires_at'   => 'datetime',
            'used_at'      => 'datetime',
            'revoked_at'   => 'datetime',
        ];
    }

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function tracerStudy(): BelongsTo
    {
        return $this->belongsTo(TracerStudy::class);
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function isValid(): bool
    {
        return ! $this->is_used
            && ! $this->is_revoked
            && ! $this->trashed()
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}