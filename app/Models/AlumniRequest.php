<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniRequest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    // ─── Enum constants (sesuai DB ENUM) ─────────────────────────────────────

    public const TYPES = [
        'update_akademik',
        'update_profil',
        'lainnya',
    ];

    public const STATUSES = [
        'menunggu',
        'disetujui',
        'ditolak',
    ];

    public const STATUS_MENUNGGU  = 'menunggu';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK   = 'ditolak';

    public const TYPE_UPDATE_AKADEMIK = 'update_akademik';
    public const TYPE_UPDATE_PROFIL   = 'update_profil';
    public const TYPE_LAINNYA         = 'lainnya';

    // ─── Mass-assignable ─────────────────────────────────────────────────────

    protected $fillable = [
        'id',
        'alumni_id',
        'type',
        'field_name',
        'old_value',
        'new_value',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopeByAlumni($query, string $alumniId)
    {
        return $query->where('alumni_id', $alumniId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }
}
