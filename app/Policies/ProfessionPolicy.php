<?php

namespace App\Policies;

use App\Models\Profession;
use App\Models\User;

class ProfessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Profession $profession): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Profession $profession): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Profession $profession): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Profession $profession): bool
    {
        return $user->isSuperAdmin();
    }
}
