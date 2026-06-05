<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'sector', 'website', 'logo', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public const TYPES = ['pemerintah', 'swasta', 'bumn', 'pendidikan', 'lainnya'];

    public function detail(): HasOne
    {
        return $this->hasOne(InstitutionDetail::class);
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(AlumniEmploymentHistory::class);
    }

    public function employerTokens(): HasMany
    {
        return $this->hasMany(EmployerAccessToken::class);
    }
}