<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumni extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'alumni';

    protected $fillable = [
        'user_id',
        'study_program_id',
        'nim',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'graduation_year',
        'graduation_date',
        'ipk',
        'thesis_title',
        'photo',
        'employment_status',
        'waiting_period_months',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'graduation_date' => 'date',
            'ipk' => 'decimal:2',
            'is_employed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(AlumniEmploymentHistory::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(AlumniRequest::class);
    }

    public function employerTokens(): HasMany
    {
        return $this->hasMany(EmployerAccessToken::class);
    }
}