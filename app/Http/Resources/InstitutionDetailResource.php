<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'institution_id'   => $this->institution_id,
            'website'          => $this->website,
            'phone'            => $this->phone,
            'email'            => $this->email,
            'address'          => $this->address,
            'city'             => $this->city,
            'province'         => $this->province,
            'postal_code'      => $this->postal_code,
            'description'      => $this->description,
            'logo_url'         => $this->logo_url,
            'linkedin_url'     => $this->linkedin_url,
            'updated_by'       => $this->updated_by,
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
