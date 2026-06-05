<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        /*
         * Gate 'admin' — hanya role 'super_admin' yang aktif.
         * Digunakan di: Route::middleware('can:admin') dan $this->authorize('admin').
         *
         * KONSISTENSI: role string adalah 'super_admin' (bukan 'admin'),
         * sesuai 02_DATABASE.md kolom users.role ENUM('super_admin','alumni','pengguna_alumni').
         */
        Gate::define('admin', static function (User $user): bool {
            return $user->role === 'super_admin' && (bool) $user->is_active;
        });

        /*
         * Gate 'alumni' — hanya role 'alumni' yang aktif.
         */
        Gate::define('alumni', static function (User $user): bool {
            return $user->role === 'alumni' && (bool) $user->is_active;
        });

        /*
         * Gate 'employer' — role 'pengguna_alumni' yang aktif.
         * Digunakan di route employer portal.
         */
        Gate::define('employer', static function (User $user): bool {
            return $user->role === 'pengguna_alumni'
                && (bool) $user->is_active
                && $user->tokenCan('employer');
        });
    }
}