<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmployerOtpRequest;
use App\Http\Requests\Auth\EmployerOtpVerifyRequest;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;

class EmployerAccessController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly OtpService $otpService
    ) {
    }

    public function requestOtp(EmployerOtpRequest $request): JsonResponse
    {
        $result = $this->authService->requestEmployerOtp(
            plainToken: $request->string('token')->value(),
            otpService: $this->otpService,
        );

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'data' => [
                'contact_masked' => $result['contact_masked'],
                'expires_in' => $result['expires_in'],
            ],
        ]);
    }

    public function verifyOtp(EmployerOtpVerifyRequest $request): JsonResponse
    {
        $result = $this->authService->verifyEmployerOtp(
            plainToken: $request->string('token')->value(),
            otpCode: $request->string('otp_code')->value(),
            otpService: $this->otpService,
            ipAddress: $request->ip(),
        );

        return response()->json([
            'status' => true,
            'message' => 'Verifikasi OTP employer berhasil.',
            'data' => $result,
        ]);
    }
}