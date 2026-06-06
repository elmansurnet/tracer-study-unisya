<?php

namespace App\Policies;

use App\Models\ProfessionCategory;
use App\Models\User;

class ProfessionCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, ProfessionCategory $category): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, ProfessionCategory $category): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ProfessionCategory $category): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, ProfessionCategory $category): bool
    {
        return $user->isSuperAdmin();
    }
}
