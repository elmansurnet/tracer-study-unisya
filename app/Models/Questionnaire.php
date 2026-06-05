<?php
// app/Models/Questionnaire.php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'questionnaire_category_id', 'title', 'description',
        'respondent_type', 'scope',
        'faculty_id', 'study_program_id',
        'start_date', 'end_date', 'is_active', 'version',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_active'  => 'boolean',
        ];
    }

    public const SCOPES          = ['global', 'faculty', 'study_program'];
    public const RESPONDENT_TYPES = ['alumni', 'employer', 'both'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireCategory::class, 'questionnaire_category_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionnaireQuestion::class)->orderBy('question_order');
    }
}