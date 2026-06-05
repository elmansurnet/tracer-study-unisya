<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudyProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'faculty_id'   => ['required', 'string', 'max:36'],
            'code'         => ['required', 'string', 'max:20'],
            'name'         => ['required', 'string', 'max:255'],
            'degree_level' => ['required', Rule::in(['D3', 'S1', 'S2', 'S3', 'Profesi'])],
            'description'  => ['nullable', 'string', 'max:1000'],
            'is_active'    => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'faculty_id'   => 'Fakultas',
            'code'         => 'Kode Program Studi',
            'name'         => 'Nama Program Studi',
            'degree_level' => 'Jenjang',
            'description'  => 'Deskripsi',
            'is_active'    => 'Status Aktif',
        ];
    }
}
