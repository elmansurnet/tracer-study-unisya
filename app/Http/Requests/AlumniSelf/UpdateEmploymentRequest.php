<?php

namespace App\Http\Requests\AlumniSelf;

use App\Models\Alumni;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAlumni() && $this->user()?->alumni !== null;
    }

    public function rules(): array
    {
        return [
            'employment_status'     => ['required', Rule::in(Alumni::EMPLOYMENT_STATUSES)],
            'is_employed'           => ['required', 'boolean'],
            'waiting_period_months' => ['nullable', 'integer', 'min:0', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employment_status'     => 'Status Pekerjaan',
            'is_employed'           => 'Status Bekerja',
            'waiting_period_months' => 'Masa Tunggu (Bulan)',
        ];
    }
}
