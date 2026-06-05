<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OtpRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'identifier_type' => ['required', Rule::in(['email', 'whatsapp'])],
            'purpose' => ['required', Rule::in([
                'login',
                'employer_access',
                'phone_verify',
                'email_verify',
            ])],
            'reference_id' => ['nullable', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Email atau nomor WhatsApp wajib diisi.',
            'identifier_type.in' => 'Tipe identifier harus email atau whatsapp.',
            'purpose.in' => 'Purpose OTP tidak valid.',
            'reference_id.uuid' => 'Reference ID harus berupa UUID yang valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'identifier' => is_string($this->identifier)
                ? trim($this->identifier)
                : $this->identifier,
            'purpose' => is_string($this->purpose)
                ? trim($this->purpose)
                : $this->purpose,
        ]);
    }
}