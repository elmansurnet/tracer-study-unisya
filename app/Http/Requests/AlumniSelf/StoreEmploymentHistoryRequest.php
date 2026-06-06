<?php

namespace App\Http\Requests\AlumniSelf;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmploymentHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAlumni() && $this->user()?->alumni !== null;
    }

    public function rules(): array
    {
        return [
            'institution_id' => ['nullable', 'string', 'max:36'],
            'profession_id'  => ['nullable', 'string', 'max:36'],
            'job_title'      => ['nullable', 'string', 'max:255'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current'     => ['sometimes', 'boolean'],
            'salary_range'   => ['nullable', 'string', 'max:100'],
            'job_relevance'  => ['nullable', Rule::in(['sangat_relevan', 'relevan', 'kurang_relevan', 'tidak_relevan'])],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_id' => 'Institusi',
            'profession_id'  => 'Profesi',
            'job_title'      => 'Jabatan',
            'start_date'     => 'Tanggal Mulai',
            'end_date'       => 'Tanggal Selesai',
            'is_current'     => 'Pekerjaan Saat Ini',
            'salary_range'   => 'Rentang Gaji',
            'job_relevance'  => 'Relevansi Pekerjaan',
            'notes'          => 'Catatan',
        ];
    }
}
