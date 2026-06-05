<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->currentAccessToken() || ! $user->tokenCan('employer')) {
            return response()->json([
                'status' => false,
                'message' => 'Akses tidak diizinkan. Token employer diperlukan.',
            ], 403);
        }

        return $next($request);
    }
}