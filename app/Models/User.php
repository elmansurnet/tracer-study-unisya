<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * Mass-assignable fields.
     *
     * CATATAN KEAMANAN:
     * - 'role' disertakan agar seeder/factory dapat mengisi field ini.
     *   Di production, field ini TIDAK boleh diisi langsung dari request user.
     *   Selalu gunakan forceFill() atau assignment eksplisit di Service/Controller.
     * - 'password' tidak perlu di sini karena sudah di-cast 'hashed'.
     * - 'last_login_at' dan 'last_login_ip' diisi via forceFill() di AuthService,
     *   namun tetap didaftarkan agar tidak tertolak saat Unit Test / seeder.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function alumni(): HasOne
    {
        return $this->hasOne(Alumni::class, 'user_id');
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAlumni(): bool
    {
        return $this->role === 'alumni';
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->isSuperAdmin() && $this->isActive();
    }

    public function canAccessAlumniPanel(): bool
    {
        return $this->isAlumni() && $this->isActive();
    }
}