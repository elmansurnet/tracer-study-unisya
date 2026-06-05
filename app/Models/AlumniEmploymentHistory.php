<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniEmploymentHistory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
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
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_current' => 'boolean',
        ];
    }

    // Nilai enum yang valid — digunakan oleh Request Validation di phase berikutnya
    public const SALARY_RANGES = ['<1jt', '1-3jt', '3-5jt', '5-10jt', '>10jt'];
    public const JOB_RELEVANCES = ['sangat_relevan', 'relevan', 'kurang_relevan', 'tidak_relevan'];

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
}