<?php

namespace App\Policies;

use App\Models\InstitutionDetail;
use App\Models\User;

class InstitutionDetailPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('operator');
    }

    public function view(User $user, InstitutionDetail $detail): bool
    {
        return $user->hasRole('admin') || $user->hasRole('operator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('operator');
    }

    public function update(User $user, InstitutionDetail $detail): bool
    {
        return $user->hasRole('admin') || $user->hasRole('operator');
    }

    public function delete(User $user, InstitutionDetail $detail): bool
    {
        return $user->hasRole('admin');
    }
}
