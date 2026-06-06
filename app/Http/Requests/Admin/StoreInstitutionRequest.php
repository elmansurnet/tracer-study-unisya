<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'type'      => ['required', 'string', Rule::in(['pemerintah', 'swasta', 'bumn', 'pendidikan', 'lainnya'])],
            'sector'    => ['nullable', 'string', 'max:255'],
            'website'   => ['nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],

            // Detail (opsional saat buat)
            'detail'                  => ['sometimes', 'array'],
            'detail.address'          => ['nullable', 'string', 'max:1000'],
            'detail.city'             => ['nullable', 'string', 'max:100'],
            'detail.province'         => ['nullable', 'string', 'max:100'],
            'detail.postal_code'      => ['nullable', 'string', 'max:10'],
            'detail.phone'            => ['nullable', 'string', 'max:20'],
            'detail.fax'              => ['nullable', 'string', 'max:20'],
            'detail.email'            => ['nullable', 'email', 'max:255'],
            'detail.contact_person'   => ['nullable', 'string', 'max:255'],
            'detail.contact_phone'    => ['nullable', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'Nama Institusi',
            'type'                  => 'Tipe Institusi',
            'sector'                => 'Sektor',
            'website'               => 'Website',
            'is_active'             => 'Status Aktif',
            'detail.address'        => 'Alamat',
            'detail.city'           => 'Kota',
            'detail.province'       => 'Provinsi',
            'detail.postal_code'    => 'Kode Pos',
            'detail.phone'          => 'Telepon',
            'detail.fax'            => 'Fax',
            'detail.email'          => 'Email',
            'detail.contact_person' => 'Nama PIC',
            'detail.contact_phone'  => 'HP PIC',
        ];
    }
}
