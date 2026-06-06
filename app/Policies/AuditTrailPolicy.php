<?php

namespace App\Policies;

use App\Models\AuditTrail;
use App\Models\User;

class AuditTrailPolicy
{
    /**
     * Hanya super_admin yang bisa melihat audit trail.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin' && $user->is_active;
    }

    public function view(User $user, AuditTrail $auditTrail): bool
    {
        return $user->role === 'super_admin' && $user->is_active;
    }

    /**
     * Hanya super_admin yang bisa purge activity log.
     */
    public function purge(User $user): bool
    {
        return $user->role === 'super_admin' && $user->is_active;
    }
}
