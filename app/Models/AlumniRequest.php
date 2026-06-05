<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniRequest extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'alumni_id', 'type', 'field_name',
        'old_value', 'new_value', 'reason', 'status',
        'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public const TYPES   = ['update_akademik', 'update_profil', 'lainnya'];
    public const STATUSES = ['menunggu', 'disetujui', 'ditolak'];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}