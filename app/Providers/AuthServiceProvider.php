<?php

namespace App\Providers;

use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use App\Policies\FacultyPolicy;
use App\Policies\StudyProgramPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        User::class          => UserPolicy::class,
        Faculty::class       => FacultyPolicy::class,
        StudyProgram::class  => StudyProgramPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate global: super_admin dapat mengakses semua route admin
        Gate::define('admin', function (User $user): bool {
            return $user->role === 'super_admin' && $user->is_active;
        });

        // Gate global: alumni dapat mengakses route alumni
        Gate::define('alumni', function (User $user): bool {
            return $user->role === 'alumni' && $user->is_active;
        });
    }
}
