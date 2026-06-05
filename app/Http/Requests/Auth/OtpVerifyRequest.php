<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OtpVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier'      => ['required', 'string', 'max:255'],
            /*
             * PATCH: field diganti dari 'otp' menjadi 'otp_code' agar
             * konsisten dengan:
             *   - 05_API.md  → Body: { identifier, otp_code }
             *   - OtpController::verify() → $request->string('otp_code')
             *   - EmployerOtpVerifyRequest → field 'otp_code'
             */
            'otp_code'        => ['required', 'digits:6'],
            'identifier_type' => ['nullable', Rule::in(['email', 'whatsapp'])],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required'  => 'Email atau nomor WhatsApp wajib diisi.',
            'otp_code.required'    => 'Kode OTP wajib diisi.',
            'otp_code.digits'      => 'Kode OTP harus terdiri dari 6 digit angka.',
            'identifier_type.in'   => 'Tipe identifier harus email atau whatsapp.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'identifier' => is_string($this->identifier)
                ? trim($this->identifier)
                : $this->identifier,
            'otp_code'   => is_string($this->otp_code)
                ? preg_replace('/\D+/', '', $this->otp_code)
                : $this->otp_code,
        ]);
    }
}