<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniEmploymentHistory extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'alumni_employment_histories';

    // ─── Enum constants ──────────────────────────────────────────────────────

    public const SALARY_RANGES    = ['<1jt', '1-3jt', '3-5jt', '5-10jt', '>10jt'];
    public const JOB_RELEVANCES   = ['sangat_relevan', 'relevan', 'kurang_relevan', 'tidak_relevan'];

    // ─── Mass-assignable ─────────────────────────────────────────────────────

    protected $fillable = [
        'id',
        'alumni_id',
        'institution_id',
        'profession_id',
        'job_title',
        'start_date',
        'end_date',
        'is_current',
        'salary_range',
        'job_relevance',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_current'  => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
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

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeForAlumni($query, string $alumniId)
    {
        return $query->where('alumni_id', $alumniId);
    }

    public function scopeByInstitution($query, string $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByProfession($query, string $professionId)
    {
        return $query->where('profession_id', $professionId);
    }
}
