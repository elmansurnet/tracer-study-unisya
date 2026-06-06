<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnswerType extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'answer_types';

    /**
     * Built-in answer type codes — used as constants
     * so that seeders and services reference one source of truth.
     */
    public const TEXT        = 'text';
    public const TEXTAREA    = 'textarea';
    public const RADIO       = 'radio';
    public const CHECKBOX    = 'checkbox';
    public const SELECT      = 'select';
    public const SCALE       = 'scale';
    public const DATE        = 'date';
    public const NUMBER      = 'number';

    // ─── Mass-assignable ─────────────────────────────────────────────────────

    protected $fillable = [
        'code',
        'name',
        'description',
        'config',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────

    protected $casts = [
        'config'     => 'array',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionnaireQuestion::class, 'answer_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
