<?php

namespace App\Providers;

use App\Models\Alumni;
use App\Models\AlumniEmploymentHistory;
use App\Models\AppSetting;
use App\Models\AuditTrail;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\InstitutionDetail;
use App\Models\Profession;
use App\Models\ProfessionCategory;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\AnswerType;
use App\Models\StudyProgram;
use App\Models\User;
use App\Policies\AlumniEmploymentHistoryPolicy;
use App\Policies\AlumniPolicy;
use App\Policies\AppSettingPolicy;
use App\Policies\AuditTrailPolicy;
use App\Policies\AnswerTypePolicy;
use App\Policies\FacultyPolicy;
use App\Policies\InstitutionDetailPolicy;
use App\Policies\InstitutionPolicy;
use App\Policies\ProfessionCategoryPolicy;
use App\Policies\ProfessionPolicy;
use App\Policies\QuestionnaireCategoryPolicy;
use App\Policies\QuestionnairePolicy;
use App\Policies\QuestionnaireQuestionPolicy;
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
        User::class                    => UserPolicy::class,
        Faculty::class                 => FacultyPolicy::class,
        StudyProgram::class            => StudyProgramPolicy::class,
        Profession::class              => ProfessionPolicy::class,
        ProfessionCategory::class      => ProfessionCategoryPolicy::class,
        Institution::class             => InstitutionPolicy::class,
        InstitutionDetail::class       => InstitutionDetailPolicy::class,
        AuditTrail::class              => AuditTrailPolicy::class,
        Activity::class                => AuditTrailPolicy::class,
        AppSetting::class              => AppSettingPolicy::class,
        Alumni::class                  => AlumniPolicy::class,
        AlumniEmploymentHistory::class => AlumniEmploymentHistoryPolicy::class,
        // Phase 4A
        QuestionnaireCategory::class   => QuestionnaireCategoryPolicy::class,
        AnswerType::class              => AnswerTypePolicy::class,
        Questionnaire::class           => QuestionnairePolicy::class,
        QuestionnaireQuestion::class   => QuestionnaireQuestionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate global: super_admin dapat mengakses semua route admin
        Gate::define('admin', function (User $user): bool {
            return $user->role === 'super_admin' && $user->is_active;
        });

        // Gate global: alumni dapat mengakses route alumni self-service
        Gate::define('alumni', function (User $user): bool {
            return $user->role === 'alumni' && $user->is_active;
        });
    }
}
