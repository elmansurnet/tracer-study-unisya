<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $isLocal = app()->environment('local');

        if ($isLocal) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://127.0.0.1:5173 http://tracer-study-unisya.test:5173; " .
                "style-src 'self' 'unsafe-inline' http://127.0.0.1:5173 http://tracer-study-unisya.test:5173 https://fonts.googleapis.com; " .
                "font-src 'self' https://fonts.gstatic.com data:; " .
                "img-src 'self' data: blob:; " .
                "connect-src 'self' ws://127.0.0.1:5173 ws://tracer-study-unisya.test:5173 http://127.0.0.1:5173 http://tracer-study-unisya.test:5173"
            );
        } else {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline'; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                "font-src 'self' https://fonts.gstatic.com; " .
                "img-src 'self' data: blob:; " .
                "connect-src 'self'"
            );
        }

        return $response;
    }
}