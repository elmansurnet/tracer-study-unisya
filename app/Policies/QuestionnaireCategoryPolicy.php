<?php

namespace App\Policies;

use App\Models\QuestionnaireCategory;
use App\Models\User;

class QuestionnaireCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function view(User $user, QuestionnaireCategory $category): bool
    {
        return $user->role === 'super_admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function update(User $user, QuestionnaireCategory $category): bool
    {
        return $user->role === 'super_admin';
    }

    public function delete(User $user, QuestionnaireCategory $category): bool
    {
        return $user->role === 'super_admin';
    }

    public function restore(User $user, QuestionnaireCategory $category): bool
    {
        return $user->role === 'super_admin';
    }

    public function forceDelete(User $user, QuestionnaireCategory $category): bool
    {
        return $user->role === 'super_admin';
    }
}
