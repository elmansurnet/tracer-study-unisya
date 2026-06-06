<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireQuestion extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'questionnaire_questions';

    // ─── Mass-assignable ─────────────────────────────────────────────────────

    protected $fillable = [
        'questionnaire_id',
        'answer_type_id',
        'question_text',
        'question_order',
        'is_required',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────

    protected $casts = [
        'question_order' => 'integer',
        'is_required'    => 'boolean',
        'is_active'      => 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }

    public function answerType(): BelongsTo
    {
        return $this->belongsTo(AnswerType::class, 'answer_type_id');
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('question_order');
    }
}
