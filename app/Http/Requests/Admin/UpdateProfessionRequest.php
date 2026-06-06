<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'profession_category_id' => [
                'sometimes',
                'required',
                'string',
                Rule::exists('profession_categories', 'id')->whereNull('deleted_at'),
            ],
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'profession_category_id' => 'Kategori Profesi',
            'name'                   => 'Nama Profesi',
            'description'            => 'Deskripsi',
            'is_active'              => 'Status Aktif',
        ];
    }
}
