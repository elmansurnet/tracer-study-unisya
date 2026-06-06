<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'type'       => $this->type,
            'sector'     => $this->sector,
            'website'    => $this->website,
            'logo'       => $this->logo,
            'is_active'  => $this->is_active,
            'detail'     => $this->whenLoaded('detail'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
