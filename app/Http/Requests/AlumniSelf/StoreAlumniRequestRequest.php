<?php

namespace App\Http\Requests\AlumniSelf;

use App\Models\AlumniRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlumniRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi ditangani di controller via Policy
    }

    public function rules(): array
    {
        return [
            // alumni_id TIDAK diterima dari body — di-inject dari sesi di controller
            'type'       => ['required', 'string', Rule::in(AlumniRequest::TYPES)],
            'field_name' => ['required', 'string', 'max:100'],
            'old_value'  => ['nullable', 'string', 'max:1000'],
            'new_value'  => ['required', 'string', 'max:1000'],
            'reason'     => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'       => 'Tipe permohonan wajib diisi.',
            'type.in'             => 'Tipe permohonan tidak valid.',
            'field_name.required' => 'Nama field yang ingin diubah wajib diisi.',
            'new_value.required'  => 'Nilai baru wajib diisi.',
        ];
    }
}
