<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'role'               => $this->role,
            'is_active'          => $this->is_active,
            'email_verified_at'  => $this->email_verified_at?->toIso8601String(),
            'phone_verified_at'  => $this->phone_verified_at?->toIso8601String(),
            'last_login_at'      => $this->last_login_at?->toIso8601String(),
            'last_login_ip'      => $this->last_login_ip,
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
            'deleted_at'         => $this->deleted_at?->toIso8601String(),
            'created_by'         => $this->created_by,
            'updated_by'         => $this->updated_by,
        ];
    }
}
