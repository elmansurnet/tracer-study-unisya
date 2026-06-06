<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\User;

class AlumniPolicy
{
    /**
     * Admin: lihat daftar semua alumni.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Admin: lihat detail alumni mana pun.
     * Alumni: hanya boleh lihat data dirinya sendiri.
     */
    public function view(User $user, Alumni $alumni): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAlumni() && $user->id === $alumni->user_id;
    }

    /**
     * Hanya super_admin yang boleh membuat data alumni baru.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Admin: update alumni mana pun.
     * Alumni: update data dirinya sendiri (via updateSelf).
     */
    public function update(User $user, Alumni $alumni): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Alumni self-service: alumni hanya boleh update profil & status pekerjaan dirinya.
     */
    public function updateSelf(User $user, Alumni $alumni): bool
    {
        return $user->isAlumni() && $user->id === $alumni->user_id;
    }

    /**
     * Hanya super_admin yang boleh soft-delete alumni.
     */
    public function delete(User $user, Alumni $alumni): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Hanya super_admin yang boleh restore alumni yang sudah dihapus.
     */
    public function restore(User $user, Alumni $alumni): bool
    {
        return $user->isSuperAdmin();
    }
}
