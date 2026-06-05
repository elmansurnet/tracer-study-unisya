<?php
// app/Models/TracerStudy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TracerStudy extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'academic_year',
        'start_date', 'end_date', 'status',
        'target_scope', 'target_faculty_id', 'target_study_program_id',
        'target_graduation_years',
    ];

    protected function casts(): array
    {
        return [
            'start_date'              => 'date',
            'end_date'                => 'date',
            'target_graduation_years' => 'array',
        ];
    }

    public const STATUSES      = ['draft', 'aktif', 'selesai', 'dibatalkan'];
    public const TARGET_SCOPES = ['all', 'faculty', 'study_program'];

    public function targetFaculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'target_faculty_id');
    }

    public function targetStudyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'target_study_program_id');
    }

    public function questionnaires(): BelongsToMany
    {
        return $this->belongsToMany(Questionnaire::class, 'tracer_study_questionnaires')
                    ->withPivot('order')
                    ->orderByPivot('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }
}