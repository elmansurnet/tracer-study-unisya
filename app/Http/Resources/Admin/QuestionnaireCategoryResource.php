<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'is_active'   => $this->is_active,
            'created_by'  => $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator?->id,
                'name' => $this->creator?->name,
            ]),
            'updated_by'  => $this->whenLoaded('updater', fn () => [
                'id'   => $this->updater?->id,
                'name' => $this->updater?->name,
            ]),
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
            'deleted_at'  => $this->deleted_at?->toISOString(),
        ];
    }
}
