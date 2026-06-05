<?php

namespace App\Providers;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('auth-login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(fn () => response()->json([
                    'status'  => false,
                    'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.',
                ], 429));
        });

        RateLimiter::for('otp-request', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->input('identifier', $request->ip()));
        });

        RateLimiter::for('otp-verify', function (Request $request) {
            return Limit::perMinutes(5, 5)
                ->by($request->input('identifier', $request->ip()));
        });

        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('export', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()?->id ?? $request->ip());
        });

        RateLimiter::for('import', function (Request $request) {
            return Limit::perMinutes(5, 3)
                ->by($request->user()?->id ?? $request->ip());
        });
    }
}