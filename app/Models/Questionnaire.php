<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'questionnaires';

    // ─── Enum constants ──────────────────────────────────────────────────────

    public const RESPONDENT_TYPES = ['alumni', 'employer', 'both'];
    public const SCOPES           = ['global', 'faculty', 'study_program'];

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
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
        'version'    => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
                    ->whereNull('deleted_at')
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

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionnaireResponse::class, 'questionnaire_id');
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

    public function scopeByRespondentType($query, string $type)
    {
        return $query->where('respondent_type', $type);
    }

    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeForAlumni($query)
    {
        return $query->whereIn('respondent_type', ['alumni', 'both']);
    }

    public function scopeForEmployer($query)
    {
        return $query->whereIn('respondent_type', ['employer', 'both']);
    }
}
