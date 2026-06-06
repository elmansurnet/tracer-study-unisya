<?php

namespace App\Http\Requests\Admin;

use App\Models\Alumni;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'study_program_id'      => ['sometimes', 'string', 'max:36'],
            'user_id'               => ['sometimes', 'nullable', 'string', 'max:36'],
            'nim'                   => ['sometimes', 'string', 'max:20'],
            'name'                  => ['sometimes', 'string', 'max:255'],
            'gender'                => ['sometimes', Rule::in(Alumni::GENDERS)],
            'birth_place'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'birth_date'            => ['sometimes', 'nullable', 'date', 'before:today'],
            'address'               => ['sometimes', 'nullable', 'string', 'max:500'],
            'city'                  => ['sometimes', 'nullable', 'string', 'max:100'],
            'province'              => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code'           => ['sometimes', 'nullable', 'string', 'max:10'],
            'phone'                 => ['sometimes', 'nullable', 'string', 'max:20'],
            'email'                 => ['sometimes', 'nullable', 'email', 'max:255'],
            'graduation_year'       => ['sometimes', 'integer', 'min:1945', 'max:' . (date('Y') + 1)],
            'graduation_date'       => ['sometimes', 'nullable', 'date'],
            'ipk'                   => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:4.00'],
            'thesis_title'          => ['sometimes', 'nullable', 'string', 'max:500'],
            'photo'                 => ['sometimes', 'nullable', 'string', 'max:500'],
            'is_employed'           => ['sometimes', 'boolean'],
            'employment_status'     => ['sometimes', 'nullable', Rule::in(Alumni::EMPLOYMENT_STATUSES)],
            'waiting_period_months' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'study_program_id'      => 'Program Studi',
            'user_id'               => 'User',
            'nim'                   => 'NIM',
            'name'                  => 'Nama',
            'gender'                => 'Jenis Kelamin',
            'birth_place'           => 'Tempat Lahir',
            'birth_date'            => 'Tanggal Lahir',
            'address'               => 'Alamat',
            'city'                  => 'Kota',
            'province'              => 'Provinsi',
            'postal_code'           => 'Kode Pos',
            'phone'                 => 'Nomor HP',
            'email'                 => 'Email',
            'graduation_year'       => 'Tahun Lulus',
            'graduation_date'       => 'Tanggal Wisuda',
            'ipk'                   => 'IPK',
            'thesis_title'          => 'Judul Skripsi/Tesis',
            'photo'                 => 'Foto',
            'is_employed'           => 'Status Bekerja',
            'employment_status'     => 'Status Pekerjaan',
            'waiting_period_months' => 'Masa Tunggu (Bulan)',
        ];
    }
}
