<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewAlumniRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi ditangani di controller via Policy
    }

    /**
     * Digunakan untuk endpoint approve dan reject.
     * 'action' di-inject dari route (approve/reject) di controller,
     * tapi kita terima juga dari body untuk fleksibilitas.
     */
    public function rules(): array
    {
        return [
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'review_notes.max' => 'Catatan review maksimal 1000 karakter.',
        ];
    }
}
