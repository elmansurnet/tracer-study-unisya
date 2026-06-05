<?php

use App\Http\Controllers\Api\Admin\FacultyController;
use App\Http\Controllers\Api\Admin\StudyProgramController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\EmployerAccessController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\OtpController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Auth (Public) ────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login'])
            ->middleware('throttle:auth-login');
        Route::post('/otp/request', [OtpController::class, 'request'])
            ->middleware('throttle:otp-request');
        Route::post('/otp/verify', [OtpController::class, 'verify'])
            ->middleware('throttle:otp-verify');
    });

    // ─── Employer (Public) ───────────────────────────────────────────
    Route::prefix('employer')->group(function () {
        Route::post('/otp/request', [EmployerAccessController::class, 'requestOtp'])
            ->middleware('throttle:otp-request');
        Route::post('/otp/verify', [EmployerAccessController::class, 'verifyOtp'])
            ->middleware('throttle:otp-verify');
    });

    // ─── Authenticated Routes ─────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'ensure.active'])->group(function () {
        Route::post('/auth/logout', [LoginController::class, 'logout']);
        Route::get('/auth/me',     [LoginController::class, 'me']);

        // ─── Admin Routes ──────────────────────────────────────────
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
            Route::get('/faculties/all',           [FacultyController::class, 'all']);
            Route::get('/faculties',               [FacultyController::class, 'index']);
            Route::post('/faculties',              [FacultyController::class, 'store']);
            Route::get('/faculties/{id}',          [FacultyController::class, 'show']);
            Route::put('/faculties/{id}',          [FacultyController::class, 'update']);
            Route::delete('/faculties/{id}',       [FacultyController::class, 'destroy']);
            Route::patch('/faculties/{id}/restore',[FacultyController::class, 'restore']);

            // Manajemen Program Studi
            Route::get('/study-programs/all',              [StudyProgramController::class, 'all']);
            Route::get('/study-programs',                  [StudyProgramController::class, 'index']);
            Route::post('/study-programs',                 [StudyProgramController::class, 'store']);
            Route::get('/study-programs/{id}',             [StudyProgramController::class, 'show']);
            Route::put('/study-programs/{id}',             [StudyProgramController::class, 'update']);
            Route::delete('/study-programs/{id}',          [StudyProgramController::class, 'destroy']);
            Route::patch('/study-programs/{id}/restore',   [StudyProgramController::class, 'restore']);
        });

        // ─── Alumni Routes ─────────────────────────────────────────
        Route::middleware('can:alumni')->prefix('alumni')->group(function () {
            //
        });

        // ─── Employer Routes ───────────────────────────────────────
        Route::middleware('employer.token')->prefix('employer')->group(function () {
            //
        });
    });
});
