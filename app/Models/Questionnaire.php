<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'questionnaires';

    // ─── Mass-assignable ─────────────────────────────────────────────────────

    protected $fillable = [
        'questionnaire_category_id',
        'title',
        'description',
        'respondent_type',
        'scope',
        'faculty_id',
        'study_program_id',
        'start_date',
        'end_date',
        'is_active',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_active'   => 'boolean',
        'version'     => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireCategory::class, 'questionnaire_category_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionnaireQuestion::class, 'questionnaire_id')
            ->orderBy('question_order');
    }

    public function activeQuestions(): HasMany
    {
        return $this->hasMany(QuestionnaireQuestion::class, 'questionnaire_id')
            ->where('is_active', true)
            ->orderBy('question_order');
    }

    public function tracerStudies(): BelongsToMany
    {
        return $this->belongsToMany(
            TracerStudy::class,
            'tracer_study_questionnaires',
            'questionnaire_id',
            'tracer_study_id'
        )->withPivot('order')->withTimestamps();
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
        return $query->where('is_active', true)->whereNull('deleted_at');
    }

    public function scopeForRespondent($query, string $type)
    {
        return $query->where(function ($q) use ($type) {
            $q->where('respondent_type', $type)
              ->orWhere('respondent_type', 'both');
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isAvailableNow(): bool
    {
        $today = now()->toDateString();
        $afterStart  = is_null($this->start_date) || $this->start_date->lte(now());
        $beforeEnd   = is_null($this->end_date)   || $this->end_date->gte(now());
        return $this->is_active && $afterStart && $beforeEnd;
    }
}
