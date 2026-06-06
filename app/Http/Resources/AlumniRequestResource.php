<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,

            // ─ Alumni lite (cukup untuk tabel list) ────────────────────────────
            'alumni'       => $this->whenLoaded('alumni', fn () => [
                'id'           => $this->alumni->id,
                'name'         => $this->alumni->name,
                'nim'          => $this->alumni->nim,
                'study_program' => $this->alumni->relationLoaded('studyProgram')
                    ? [
                        'id'   => $this->alumni->studyProgram?->id,
                        'name' => $this->alumni->studyProgram?->name,
                        'code' => $this->alumni->studyProgram?->code,
                    ]
                    : null,
            ]),

            // ─ Isi permohonan ────────────────────────────────────────
            'type'         => $this->type,
            'field_name'   => $this->field_name,
            'old_value'    => $this->old_value,
            'new_value'    => $this->new_value,
            'reason'       => $this->reason,

            // ─ Status & review ────────────────────────────────────
            'status'       => $this->status,
            'is_pending'   => $this->isPending(),
            'is_approved'  => $this->isApproved(),
            'is_rejected'  => $this->isRejected(),

            'reviewer'     => $this->whenLoaded('reviewer', fn () => $this->reviewer
                ? ['id' => $this->reviewer->id, 'name' => $this->reviewer->name]
                : null
            ),
            'reviewed_at'  => $this->reviewed_at?->toIso8601String(),
            'review_notes' => $this->review_notes,

            // ─ Audit ───────────────────────────────────────────────
            'creator'      => $this->whenLoaded('creator', fn () => $this->creator
                ? ['id' => $this->creator->id, 'name' => $this->creator->name]
                : null
            ),
            'updater'      => $this->whenLoaded('updater', fn () => $this->updater
                ? ['id' => $this->updater->id, 'name' => $this->updater->name]
                : null
            ),

            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}
