<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'required', 'string', 'max:255'],
            'type'      => ['sometimes', 'required', 'string', 'in:swasta,pemerintah,bumn,lainnya'],
            'sector'    => ['nullable', 'string', 'max:255'],
            'website'   => ['nullable', 'url', 'max:255'],
            'logo'      => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'      => 'Nama Institusi',
            'type'      => 'Jenis Institusi',
            'sector'    => 'Sektor',
            'website'   => 'Website',
            'logo'      => 'Logo',
            'is_active' => 'Status Aktif',
        ];
    }
}
