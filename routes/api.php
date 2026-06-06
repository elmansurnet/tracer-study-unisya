<?php

use App\Http\Controllers\Api\Admin\ActivityLogController;
use App\Http\Controllers\Api\Admin\AlumniController;
use App\Http\Controllers\Api\Admin\AlumniEmploymentHistoryController;
use App\Http\Controllers\Api\Admin\AlumniRequestController;
use App\Http\Controllers\Api\Admin\AnswerTypeController;
use App\Http\Controllers\Api\Admin\AuditTrailController;
use App\Http\Controllers\Api\Admin\FacultyController;
use App\Http\Controllers\Api\Admin\InstitutionController;
use App\Http\Controllers\Api\Admin\InstitutionDetailController;
use App\Http\Controllers\Api\Admin\ProfessionCategoryController;
use App\Http\Controllers\Api\Admin\ProfessionController;
use App\Http\Controllers\Api\Admin\QuestionnaireCategoryController;
use App\Http\Controllers\Api\Admin\QuestionnaireController;
use App\Http\Controllers\Api\Admin\QuestionnaireQuestionController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\StudyProgramController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AlumniSelf\AlumniRequestController as AlumniSelfRequestController;
use App\Http\Controllers\Api\AlumniSelf\EmploymentHistoryController;
use App\Http\Controllers\Api\AlumniSelf\ProfileController;
use App\Http\Controllers\Api\Auth\EmployerAccessController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\OtpController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Auth (Public) ──────────────────────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login'])
            ->middleware('throttle:auth-login');
        Route::post('/otp/request', [OtpController::class, 'request'])
            ->middleware('throttle:otp-request');
        Route::post('/otp/verify', [OtpController::class, 'verify'])
            ->middleware('throttle:otp-verify');
    });

    // ─── Employer (Public) ───────────────────────────────────────────────────────────────────────
    Route::prefix('employer')->group(function () {
        Route::post('/otp/request', [EmployerAccessController::class, 'requestOtp'])
            ->middleware('throttle:otp-request');
        Route::post('/otp/verify', [EmployerAccessController::class, 'verifyOtp'])
            ->middleware('throttle:otp-verify');
    });

    // ─── Authenticated Routes ──────────────────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'ensure.active'])->group(function () {
        Route::post('/auth/logout', [LoginController::class, 'logout']);
        Route::get('/auth/me',     [LoginController::class, 'me']);

        // ─── Admin Routes ─────────────────────────────────────────────────────────────────
        Route::middleware('can:admin')->prefix('admin')->group(function () {

            // Manajemen Pengguna
            Route::get('/users',                       [UserController::class, 'index']);
            Route::post('/users',                      [UserController::class, 'store']);
            Route::get('/users/{id}',                  [UserController::class, 'show']);
            Route::put('/users/{id}',                  [UserController::class, 'update']);
            Route::delete('/users/{id}',               [UserController::class, 'destroy']);
            Route::patch('/users/{id}/toggle-active',  [UserController::class, 'toggleActive']);
            Route::post('/users/{id}/reset-password',  [UserController::class, 'resetPassword']);

            // Manajemen Fakultas
            Route::get('/faculties/all',            [FacultyController::class, 'all']);
            Route::get('/faculties',                [FacultyController::class, 'index']);
            Route::post('/faculties',               [FacultyController::class, 'store']);
            Route::get('/faculties/{id}',           [FacultyController::class, 'show']);
            Route::put('/faculties/{id}',           [FacultyController::class, 'update']);
            Route::delete('/faculties/{id}',        [FacultyController::class, 'destroy']);
            Route::patch('/faculties/{id}/restore', [FacultyController::class, 'restore']);

            // Manajemen Program Studi
            Route::get('/study-programs/all',              [StudyProgramController::class, 'all']);
            Route::get('/study-programs',                  [StudyProgramController::class, 'index']);
            Route::post('/study-programs',                 [StudyProgramController::class, 'store']);
            Route::get('/study-programs/{id}',             [StudyProgramController::class, 'show']);
            Route::put('/study-programs/{id}',             [StudyProgramController::class, 'update']);
            Route::delete('/study-programs/{id}',          [StudyProgramController::class, 'destroy']);
            Route::patch('/study-programs/{id}/restore',   [StudyProgramController::class, 'restore']);

            // Alumni per Program Studi (nested read)
            Route::get('/study-programs/{studyProgramId}/alumni', [AlumniController::class, 'byStudyProgram']);

            // Manajemen Kategori Profesi
            Route::get('/profession-categories/all',            [ProfessionCategoryController::class, 'all']);
            Route::get('/profession-categories',                [ProfessionCategoryController::class, 'index']);
            Route::post('/profession-categories',               [ProfessionCategoryController::class, 'store']);
            Route::get('/profession-categories/{id}',           [ProfessionCategoryController::class, 'show']);
            Route::put('/profession-categories/{id}',           [ProfessionCategoryController::class, 'update']);
            Route::delete('/profession-categories/{id}',        [ProfessionCategoryController::class, 'destroy']);
            Route::patch('/profession-categories/{id}/restore', [ProfessionCategoryController::class, 'restore']);

            // Manajemen Profesi
            Route::get('/professions/all',            [ProfessionController::class, 'all']);
            Route::get('/professions',                [ProfessionController::class, 'index']);
            Route::post('/professions',               [ProfessionController::class, 'store']);
            Route::get('/professions/{id}',           [ProfessionController::class, 'show']);
            Route::put('/professions/{id}',           [ProfessionController::class, 'update']);
            Route::delete('/professions/{id}',        [ProfessionController::class, 'destroy']);
            Route::patch('/professions/{id}/restore', [ProfessionController::class, 'restore']);

            // Manajemen Institusi + Detail
            Route::get('/institutions/all',                    [InstitutionController::class, 'all']);
            Route::get('/institutions',                        [InstitutionController::class, 'index']);
            Route::post('/institutions',                       [InstitutionController::class, 'store']);
            Route::get('/institutions/{id}',                   [InstitutionController::class, 'show']);
            Route::put('/institutions/{id}',                   [InstitutionController::class, 'update']);
            Route::delete('/institutions/{id}',                [InstitutionController::class, 'destroy']);
            Route::patch('/institutions/{id}/restore',         [InstitutionController::class, 'restore']);
            Route::get('/institutions/{id}/detail',            [InstitutionDetailController::class, 'show']);
            Route::put('/institutions/{id}/detail',            [InstitutionDetailController::class, 'upsert']);

            // Manajemen Alumni
            Route::get('/alumni/graduation-years',             [AlumniController::class, 'graduationYears']);
            Route::get('/alumni/employment-stats',             [AlumniController::class, 'employmentStats']);
            Route::get('/alumni',                              [AlumniController::class, 'index']);
            Route::post('/alumni',                             [AlumniController::class, 'store']);
            Route::get('/alumni/{id}',                         [AlumniController::class, 'show']);
            Route::put('/alumni/{id}',                         [AlumniController::class, 'update']);
            Route::delete('/alumni/{id}',                      [AlumniController::class, 'destroy']);
            Route::patch('/alumni/{id}/restore',               [AlumniController::class, 'restore']);

            // Riwayat Pekerjaan Alumni (nested admin)
            Route::get('/alumni/{alumniId}/employment-histories',                    [AlumniEmploymentHistoryController::class, 'index']);
            Route::post('/alumni/{alumniId}/employment-histories',                   [AlumniEmploymentHistoryController::class, 'store']);
            Route::get('/alumni/{alumniId}/employment-histories/{id}',               [AlumniEmploymentHistoryController::class, 'show']);
            Route::put('/alumni/{alumniId}/employment-histories/{id}',               [AlumniEmploymentHistoryController::class, 'update']);
            Route::delete('/alumni/{alumniId}/employment-histories/{id}',            [AlumniEmploymentHistoryController::class, 'destroy']);
            Route::patch('/alumni/{alumniId}/employment-histories/{id}/restore',     [AlumniEmploymentHistoryController::class, 'restore']);

            // Permohonan Alumni (Admin)
            Route::get('/alumni-requests/count-pending',   [AlumniRequestController::class, 'countPending']);
            Route::get('/alumni-requests',                 [AlumniRequestController::class, 'index']);
            Route::post('/alumni-requests',                [AlumniRequestController::class, 'store']);
            Route::get('/alumni-requests/{id}',            [AlumniRequestController::class, 'show']);
            Route::put('/alumni-requests/{id}',            [AlumniRequestController::class, 'update']);
            Route::delete('/alumni-requests/{id}',         [AlumniRequestController::class, 'destroy']);
            Route::patch('/alumni-requests/{id}/restore',  [AlumniRequestController::class, 'restore']);
            Route::post('/alumni-requests/{id}/approve',   [AlumniRequestController::class, 'approve']);
            Route::post('/alumni-requests/{id}/reject',    [AlumniRequestController::class, 'reject']);

            // Audit Trail & Activity Log
            Route::get('/audit-trails',            [AuditTrailController::class, 'index']);
            Route::get('/audit-trails/{id}',       [AuditTrailController::class, 'show']);
            Route::get('/activity-logs',           [ActivityLogController::class, 'index']);
            Route::get('/activity-logs/{id}',      [ActivityLogController::class, 'show']);
            Route::delete('/activity-logs',        [ActivityLogController::class, 'purge']);

            // Pengaturan Aplikasi
            Route::get('/settings',                [SettingController::class, 'index']);
            Route::get('/settings/{group}/{key}',  [SettingController::class, 'show']);
            Route::put('/settings/{group}/{key}',  [SettingController::class, 'update']);
            Route::put('/settings/batch',          [SettingController::class, 'batchUpdate']);

            // ─── Phase 4A: Builder Kuesioner ────────────────────────────────────

            // Kategori Kuesioner
            Route::get('/questionnaire-categories/all',            [QuestionnaireCategoryController::class, 'all']);
            Route::get('/questionnaire-categories',                [QuestionnaireCategoryController::class, 'index']);
            Route::post('/questionnaire-categories',               [QuestionnaireCategoryController::class, 'store']);
            Route::get('/questionnaire-categories/{id}',           [QuestionnaireCategoryController::class, 'show']);
            Route::put('/questionnaire-categories/{id}',           [QuestionnaireCategoryController::class, 'update']);
            Route::delete('/questionnaire-categories/{id}',        [QuestionnaireCategoryController::class, 'destroy']);
            Route::patch('/questionnaire-categories/{id}/restore', [QuestionnaireCategoryController::class, 'restore']);

            // Tipe Jawaban
            Route::get('/answer-types/all',      [AnswerTypeController::class, 'all']);
            Route::get('/answer-types',          [AnswerTypeController::class, 'index']);
            Route::post('/answer-types',         [AnswerTypeController::class, 'store']);
            Route::get('/answer-types/{id}',     [AnswerTypeController::class, 'show']);
            Route::put('/answer-types/{id}',     [AnswerTypeController::class, 'update']);
            Route::delete('/answer-types/{id}',  [AnswerTypeController::class, 'destroy']);

            // Kuesioner
            Route::get('/questionnaires/all',            [QuestionnaireController::class, 'all']);
            Route::get('/questionnaires',                [QuestionnaireController::class, 'index']);
            Route::post('/questionnaires',               [QuestionnaireController::class, 'store']);
            Route::get('/questionnaires/{id}',           [QuestionnaireController::class, 'show']);
            Route::put('/questionnaires/{id}',           [QuestionnaireController::class, 'update']);
            Route::delete('/questionnaires/{id}',        [QuestionnaireController::class, 'destroy']);
            Route::patch('/questionnaires/{id}/restore', [QuestionnaireController::class, 'restore']);

            // Pertanyaan Kuesioner (nested)
            Route::get('/questionnaires/{questionnaireId}/questions',                 [QuestionnaireQuestionController::class, 'index']);
            Route::post('/questionnaires/{questionnaireId}/questions',                [QuestionnaireQuestionController::class, 'store']);
            Route::patch('/questionnaires/{questionnaireId}/questions/reorder',       [QuestionnaireQuestionController::class, 'reorder']);
            Route::get('/questionnaires/{questionnaireId}/questions/{id}',            [QuestionnaireQuestionController::class, 'show']);
            Route::put('/questionnaires/{questionnaireId}/questions/{id}',            [QuestionnaireQuestionController::class, 'update']);
            Route::delete('/questionnaires/{questionnaireId}/questions/{id}',         [QuestionnaireQuestionController::class, 'destroy']);
            Route::patch('/questionnaires/{questionnaireId}/questions/{id}/restore',  [QuestionnaireQuestionController::class, 'restore']);
        });

        // ─── Alumni Self-Service Routes ────────────────────────────────────────────────────────────────
        Route::middleware('can:alumni')->prefix('alumni')->group(function () {

            // Profil & Status Pekerjaan
            Route::get('/profile',                           [ProfileController::class, 'show']);
            Route::patch('/profile',                         [ProfileController::class, 'update']);
            Route::patch('/employment-status',               [ProfileController::class, 'updateEmploymentStatus']);

            // Riwayat Pekerjaan (self-managed)
            Route::get('/employment-histories',              [EmploymentHistoryController::class, 'index']);
            Route::post('/employment-histories',             [EmploymentHistoryController::class, 'store']);
            Route::get('/employment-histories/{id}',         [EmploymentHistoryController::class, 'show']);
            Route::put('/employment-histories/{id}',         [EmploymentHistoryController::class, 'update']);
            Route::delete('/employment-histories/{id}',      [EmploymentHistoryController::class, 'destroy']);
            Route::patch('/employment-histories/{id}/restore', [EmploymentHistoryController::class, 'restore']);

            // Permohonan Perubahan Data (self-service)
            Route::get('/requests',                          [AlumniSelfRequestController::class, 'index']);
            Route::post('/requests',                         [AlumniSelfRequestController::class, 'store']);
            Route::get('/requests/{id}',                     [AlumniSelfRequestController::class, 'show']);
            Route::delete('/requests/{id}',                  [AlumniSelfRequestController::class, 'cancel']);
        });

        // ─── Employer Routes ───────────────────────────────────────────────────────────────────
        Route::middleware('employer.token')->prefix('employer')->group(function () {
            //
        });
    });
});
