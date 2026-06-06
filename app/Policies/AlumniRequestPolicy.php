<?php

namespace App\Policies;

use App\Models\AlumniRequest;
use App\Models\User;

class AlumniRequestPolicy
{
    /**
     * Admin dapat melihat semua permohonan.
     * Alumni hanya dapat melihat daftar permohonan miliknya sendiri.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAlumni();
    }

    /**
     * Admin dapat melihat semua permohonan.
     * Alumni hanya dapat melihat permohonan miliknya sendiri.
     */
    public function view(User $user, AlumniRequest $alumniRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Alumni: pastikan permohonan ini milik alumni yang terhubung ke user ini
        return $user->isAlumni()
            && $user->alumni?->id === $alumniRequest->alumni_id;
    }

    /**
     * Hanya alumni yang dapat mengajukan permohonan.
     * Alumni hanya boleh mengajukan untuk dirinya sendiri (dicek di Service).
     */
    public function create(User $user): bool
    {
        return $user->isAlumni();
    }

    /**
     * Hanya admin yang dapat menyetujui permohonan.
     */
    public function approve(User $user, AlumniRequest $alumniRequest): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang dapat menolak permohonan.
     */
    public function reject(User $user, AlumniRequest $alumniRequest): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin dapat menghapus permohonan yang belum disetujui.
     * Alumni dapat membatalkan (menghapus) permohonannya sendiri selama masih menunggu.
     */
    public function delete(User $user, AlumniRequest $alumniRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isAlumni()
            && $user->alumni?->id === $alumniRequest->alumni_id
            && $alumniRequest->isPending();
    }

    /**
     * Hanya admin yang dapat memulihkan permohonan yang dihapus.
     */
    public function restore(User $user, AlumniRequest $alumniRequest): bool
    {
        return $user->isAdmin();
    }
}
