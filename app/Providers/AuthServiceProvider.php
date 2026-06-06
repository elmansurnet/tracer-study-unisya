<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\AuditTrail;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\InstitutionDetail;
use App\Models\Profession;
use App\Models\ProfessionCategory;
use App\Models\StudyProgram;
use App\Models\User;
use App\Policies\AppSettingPolicy;
use App\Policies\AuditTrailPolicy;
use App\Policies\FacultyPolicy;
use App\Policies\InstitutionDetailPolicy;
use App\Policies\InstitutionPolicy;
use App\Policies\ProfessionCategoryPolicy;
use App\Policies\ProfessionPolicy;
use App\Policies\StudyProgramPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        User::class              => UserPolicy::class,
        Faculty::class           => FacultyPolicy::class,
        StudyProgram::class      => StudyProgramPolicy::class,
        Profession::class        => ProfessionPolicy::class,
        ProfessionCategory::class => ProfessionCategoryPolicy::class,
        Institution::class       => InstitutionPolicy::class,
        InstitutionDetail::class => InstitutionDetailPolicy::class,
        AuditTrail::class        => AuditTrailPolicy::class,
        Activity::class          => AuditTrailPolicy::class, // ActivityLog berbagi policy
        AppSetting::class        => AppSettingPolicy::class,
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
