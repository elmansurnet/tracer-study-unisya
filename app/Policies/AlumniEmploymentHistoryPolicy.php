<?php

namespace App\Policies;

use App\Models\AlumniEmploymentHistory;
use App\Models\User;

class AlumniEmploymentHistoryPolicy
{
    /**
     * Admin: lihat semua riwayat pekerjaan.
     * Alumni: lihat riwayat miliknya sendiri.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAlumni();
    }

    /**
     * Admin: lihat detail riwayat mana pun.
     * Alumni: hanya lihat riwayat miliknya sendiri.
     */
    public function view(User $user, AlumniEmploymentHistory $history): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAlumni()
            && $user->alumni !== null
            && $user->alumni->id === $history->alumni_id;
    }

    /**
     * Admin: buat riwayat pekerjaan untuk alumni mana pun.
     * Alumni: buat riwayat pekerjaan dirinya sendiri.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAlumni();
    }

    /**
     * Admin: update riwayat pekerjaan mana pun.
     * Alumni: update hanya riwayat miliknya sendiri.
     */
    public function update(User $user, AlumniEmploymentHistory $history): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAlumni()
            && $user->alumni !== null
            && $user->alumni->id === $history->alumni_id;
    }

    /**
     * Admin: soft-delete riwayat pekerjaan mana pun.
     * Alumni: hapus riwayat miliknya sendiri.
     */
    public function delete(User $user, AlumniEmploymentHistory $history): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAlumni()
            && $user->alumni !== null
            && $user->alumni->id === $history->alumni_id;
    }

    /**
     * Hanya super_admin yang boleh restore riwayat yang sudah dihapus.
     */
    public function restore(User $user, AlumniEmploymentHistory $history): bool
    {
        return $user->isSuperAdmin();
    }
}
