<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumni extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'alumni';

    // ─── Enum constants ──────────────────────────────────────────────────────

    public const GENDERS = ['laki_laki', 'perempuan'];

    public const EMPLOYMENT_STATUSES = [
        'bekerja',
        'wirausaha',
        'melanjutkan_studi',
        'belum_bekerja',
    ];

    // ─── Mass-assignable ─────────────────────────────────────────────────────

    protected $fillable = [
        'id',
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
        'is_employed',
        'employment_status',
        'waiting_period_months',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────

    protected $casts = [
        'birth_date'       => 'date',
        'graduation_date'  => 'date',
        'graduation_year'  => 'integer',
        'ipk'              => 'decimal:2',
        'is_employed'      => 'boolean',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

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

    public function tracerStudies(): HasMany
    {
        return $this->hasMany(TracerStudy::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeEmployed($query)
    {
        return $query->where('is_employed', true);
    }

    public function scopeByGraduationYear($query, int $year)
    {
        return $query->where('graduation_year', $year);
    }

    public function scopeByStudyProgram($query, string $studyProgramId)
    {
        return $query->where('study_program_id', $studyProgramId);
    }

    public function scopeByEmploymentStatus($query, string $status)
    {
        return $query->where('employment_status', $status);
    }
}
