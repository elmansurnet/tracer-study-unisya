<?php

use App\Http\Controllers\Web\SpaController;
use Illuminate\Support\Facades\Route;

// SPA catch-all — semua URL diarahkan ke Vue SPA
Route::get('/{any}', [SpaController::class, 'index'])
    ->where('any', '.*')
    ->name('spa');