<?php

namespace App\Http\Requests\Admin;

use App\Models\Alumni;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'study_program_id'      => ['required', 'string', 'max:36'],
            'user_id'               => ['nullable', 'string', 'max:36'],
            'nim'                   => ['required', 'string', 'max:20'],
            'name'                  => ['required', 'string', 'max:255'],
            'gender'                => ['required', Rule::in(Alumni::GENDERS)],
            'birth_place'           => ['nullable', 'string', 'max:100'],
            'birth_date'            => ['nullable', 'date', 'before:today'],
            'address'               => ['nullable', 'string', 'max:500'],
            'city'                  => ['nullable', 'string', 'max:100'],
            'province'              => ['nullable', 'string', 'max:100'],
            'postal_code'           => ['nullable', 'string', 'max:10'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'email'                 => ['nullable', 'email', 'max:255'],
            'graduation_year'       => ['required', 'integer', 'min:1945', 'max:' . (date('Y') + 1)],
            'graduation_date'       => ['nullable', 'date'],
            'ipk'                   => ['nullable', 'numeric', 'min:0', 'max:4.00'],
            'thesis_title'          => ['nullable', 'string', 'max:500'],
            'photo'                 => ['nullable', 'string', 'max:500'],
            'is_employed'           => ['sometimes', 'boolean'],
            'employment_status'     => ['nullable', Rule::in(Alumni::EMPLOYMENT_STATUSES)],
            'waiting_period_months' => ['nullable', 'integer', 'min:0', 'max:120'],
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
