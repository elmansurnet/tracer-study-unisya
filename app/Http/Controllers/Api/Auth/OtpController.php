<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OtpRequestRequest;
use App\Http\Requests\Auth\OtpVerifyRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class OtpController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function request(OtpRequestRequest $request): JsonResponse
    {
        return response()->json(
            $this->authService->requestLoginOtp(
                identifier: $request->string('identifier')->toString(),
                identifierType: $request->string('identifier_type')->toString(),
                ipAddress: $request->ip()
            )
        );
    }

    public function verify(OtpVerifyRequest $request): JsonResponse
    {
        return response()->json(
            $this->authService->loginWithOtp(
                identifier: $request->string('identifier')->toString(),
                otpCode: $request->string('otp_code')->toString(),
                request: $request,
                identifierType: $request->string('identifier_type')->toString()
            )
        );
    }
}