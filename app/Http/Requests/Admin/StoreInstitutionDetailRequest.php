<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstitutionDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi ditangani di controller via Policy
    }

    public function rules(): array
    {
        return [
            'website'      => ['nullable', 'url', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:25'],
            'email'        => ['nullable', 'email', 'max:255'],
            'address'      => ['nullable', 'string', 'max:500'],
            'city'         => ['nullable', 'string', 'max:100'],
            'province'     => ['nullable', 'string', 'max:100'],
            'postal_code'  => ['nullable', 'string', 'max:10'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'logo_url'     => ['nullable', 'url', 'max:500'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'website.url'      => 'Format URL website tidak valid.',
            'email.email'      => 'Format email tidak valid.',
            'logo_url.url'     => 'Format URL logo tidak valid.',
            'linkedin_url.url' => 'Format URL LinkedIn tidak valid.',
        ];
    }
}
