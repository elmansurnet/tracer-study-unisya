<?php

namespace App\Policies;

use App\Models\AnswerType;
use App\Models\User;

class AnswerTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    public function view(User $user, AnswerType $answerType): bool
    {
        return $user->role === 'superadmin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    public function update(User $user, AnswerType $answerType): bool
    {
        return $user->role === 'superadmin';
    }

    public function delete(User $user, AnswerType $answerType): bool
    {
        return $user->role === 'superadmin';
    }
}
