<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'code'        => ['required', 'string', 'max:20'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code'        => 'Kode Fakultas',
            'name'        => 'Nama Fakultas',
            'description' => 'Deskripsi',
            'is_active'   => 'Status Aktif',
        ];
    }
}
