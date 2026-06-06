<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniEmploymentHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'alumni_id'      => $this->alumni_id,
            'institution_id' => $this->institution_id,
            'profession_id'  => $this->profession_id,

            // Detail pekerjaan
            'job_title'      => $this->job_title,
            'start_date'     => $this->start_date?->toDateString(),
            'end_date'       => $this->end_date?->toDateString(),
            'is_current'     => $this->is_current,
            'salary_range'   => $this->salary_range,
            'job_relevance'  => $this->job_relevance,
            'notes'          => $this->notes,

            // Relasi — hanya dimuat jika eager-loaded
            'institution' => $this->whenLoaded('institution', fn () => [
                'id'   => $this->institution->id,
                'name' => $this->institution->name,
                'type' => $this->institution->type ?? null,
            ]),

            'profession' => $this->whenLoaded('profession', fn () => [
                'id'   => $this->profession->id,
                'name' => $this->profession->name,
                'category' => $this->profession->relationLoaded('category') ? [
                    'id'   => $this->profession->category->id,
                    'name' => $this->profession->category->name,
                ] : null,
            ]),

            'alumni' => $this->whenLoaded('alumni', fn () => [
                'id'  => $this->alumni->id,
                'nim' => $this->alumni->nim,
                'name' => $this->alumni->name,
            ]),

            // Audit
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
