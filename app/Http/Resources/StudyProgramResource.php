<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'faculty_id'   => $this->faculty_id,
            'code'         => $this->code,
            'name'         => $this->name,
            'degree_level' => $this->degree_level,
            'description'  => $this->description,
            'is_active'    => $this->is_active,
            'faculty'      => $this->whenLoaded('faculty', fn () => [
                'id'   => $this->faculty->id,
                'code' => $this->faculty->code,
                'name' => $this->faculty->name,
            ]),
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
            'deleted_at'   => $this->deleted_at?->toIso8601String(),
            'created_by'   => $this->created_by,
            'updated_by'   => $this->updated_by,
        ];
    }
}
