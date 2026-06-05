<?php

namespace App\Policies;

use App\Models\StudyProgram;
use App\Models\User;

class StudyProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, StudyProgram $studyProgram): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, StudyProgram $studyProgram): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, StudyProgram $studyProgram): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, StudyProgram $studyProgram): bool
    {
        return $user->isSuperAdmin();
    }
}
