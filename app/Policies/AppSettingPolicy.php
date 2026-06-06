<?php

namespace App\Policies;

use App\Models\AppSetting;
use App\Models\User;

class AppSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin' && $user->is_active;
    }

    public function view(User $user, AppSetting $setting): bool
    {
        return $user->role === 'super_admin' && $user->is_active;
    }

    public function update(User $user): bool
    {
        return $user->role === 'super_admin' && $user->is_active;
    }
}
