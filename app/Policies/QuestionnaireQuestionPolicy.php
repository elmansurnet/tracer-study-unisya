<?php

namespace App\Policies;

use App\Models\QuestionnaireQuestion;
use App\Models\User;

class QuestionnaireQuestionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    public function view(User $user, QuestionnaireQuestion $question): bool
    {
        return $user->role === 'superadmin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    public function update(User $user, QuestionnaireQuestion $question): bool
    {
        return $user->role === 'superadmin';
    }

    public function delete(User $user, QuestionnaireQuestion $question): bool
    {
        return $user->role === 'superadmin';
    }

    public function restore(User $user, QuestionnaireQuestion $question): bool
    {
        return $user->role === 'superadmin';
    }
}
