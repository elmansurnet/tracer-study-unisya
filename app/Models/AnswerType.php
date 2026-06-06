<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnswerType extends Model
{
    use HasFactory, HasUuids;
    // Tidak ada SoftDeletes — answer_types adalah data master referensi

    protected $table = 'answer_types';

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
