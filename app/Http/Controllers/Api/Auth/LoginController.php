<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json(
            $this->authService->login(
                email: $request->string('email')->toString(),
                password: $request->string('password')->toString(),
                request: $request
            )
        );
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(
            $this->authService->me($request->user())
        );
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json(
            $this->authService->logout($request->user())
        );
    }
}