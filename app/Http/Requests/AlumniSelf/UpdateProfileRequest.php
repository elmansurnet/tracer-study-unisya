<?php

namespace App\Http\Requests\AlumniSelf;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya alumni yang sudah login dan punya record alumni
        return $this->user()?->isAlumni() && $this->user()?->alumni !== null;
    }

    public function rules(): array
    {
        return [
            // Field yang boleh diupdate sendiri oleh alumni
            'phone'       => ['sometimes', 'nullable', 'string', 'max:20'],
            'address'     => ['sometimes', 'nullable', 'string', 'max:500'],
            'city'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'province'    => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'photo'       => ['sometimes', 'nullable', 'string', 'max:500'],
            // Field akademik TIDAK boleh diubah sendiri (nim, graduation_year, dll)
        ];
    }

    public function attributes(): array
    {
        return [
            'phone'       => 'Nomor HP',
            'address'     => 'Alamat',
            'city'        => 'Kota',
            'province'    => 'Provinsi',
            'postal_code' => 'Kode Pos',
            'photo'       => 'Foto Profil',
        ];
    }
}
