<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'description'      => $this->description,
            'is_active'        => $this->is_active,
            'professions_count'=> $this->whenCounted('professions'),
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
            'deleted_at'       => $this->deleted_at?->toIso8601String(),
            'created_by'       => $this->created_by,
            'updated_by'       => $this->updated_by,
        ];
    }
}
