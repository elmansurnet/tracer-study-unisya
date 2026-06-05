<?php

use App\Http\Controllers\Api\Auth\EmployerAccessController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\OtpController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login'])
            ->middleware('throttle:auth-login');
        Route::post('/otp/request', [OtpController::class, 'request'])
            ->middleware('throttle:otp-request');
        Route::post('/otp/verify', [OtpController::class, 'verify'])
            ->middleware('throttle:otp-verify');
    });

    Route::prefix('employer')->group(function () {
        Route::post('/otp/request', [EmployerAccessController::class, 'requestOtp'])
            ->middleware('throttle:otp-request');
        Route::post('/otp/verify', [EmployerAccessController::class, 'verifyOtp'])
            ->middleware('throttle:otp-verify');
    });

    Route::middleware(['auth:sanctum', 'ensure.active'])->group(function () {
        Route::post('/auth/logout', [LoginController::class, 'logout']);
        Route::get('/auth/me', [LoginController::class, 'me']);

        Route::middleware('can:admin')->prefix('admin')->group(function () {
            //
        });

        Route::middleware('can:alumni')->prefix('alumni')->group(function () {
            //
        });

        Route::middleware('employer.token')->prefix('employer')->group(function () {
            //
        });
    });
});