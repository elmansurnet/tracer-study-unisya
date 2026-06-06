<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlumniRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi ditangani di controller via Policy
    }

    /**
     * Hanya reason dan new_value yang boleh diubah selama masih pending.
     * Alumni tidak boleh mengganti alumni_id, type, atau field_name setelah diajukan.
     */
    public function rules(): array
    {
        return [
            'new_value' => ['sometimes', 'required', 'string', 'max:1000'],
            'reason'    => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'new_value.required' => 'Nilai baru wajib diisi.',
        ];
    }
}
