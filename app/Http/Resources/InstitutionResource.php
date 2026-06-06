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
            'detail'     => $this->whenLoaded('detail', fn () => [
                'address'        => $this->detail?->address,
                'city'           => $this->detail?->city,
                'province'       => $this->detail?->province,
                'postal_code'    => $this->detail?->postal_code,
                'phone'          => $this->detail?->phone,
                'fax'            => $this->detail?->fax,
                'email'          => $this->detail?->email,
                'contact_person' => $this->detail?->contact_person,
                'contact_phone'  => $this->detail?->contact_phone,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
