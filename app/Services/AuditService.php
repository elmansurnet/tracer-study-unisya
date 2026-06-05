<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditService
{
    /**
     * Catat audit trail untuk setiap operasi CRUD.
     */
    public function log(
        string $event,
        Model $auditable,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $actor = null
    ): void {
        // Bersihkan sensitive fields
        $sensitive = ['password', 'remember_token', 'otp_code'];

        if ($oldValues) {
            $oldValues = collect($oldValues)
                ->except($sensitive)
                ->toArray();
        }

        if ($newValues) {
            $newValues = collect($newValues)
                ->except($sensitive)
                ->toArray();
        }

        AuditTrail::create([
            'id'             => Str::ulid(),
            'user_id'        => $actor?->id,
            'user_type'      => $actor ? 'user' : 'system',
            'event'          => $event,
            'auditable_type' => get_class($auditable),
            'auditable_id'   => $auditable->getKey(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'url'            => Request::fullUrl(),
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
        ]);
    }
}
