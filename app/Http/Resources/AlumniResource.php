<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'study_program_id' => $this->study_program_id,

            // Identitas
            'nim'              => $this->nim,
            'name'             => $this->name,
            'gender'           => $this->gender,
            'birth_place'      => $this->birth_place,
            'birth_date'       => $this->birth_date?->toDateString(),

            // Kontak
            'address'          => $this->address,
            'city'             => $this->city,
            'province'         => $this->province,
            'postal_code'      => $this->postal_code,
            'phone'            => $this->phone,
            'email'            => $this->email,

            // Akademik
            'graduation_year'  => $this->graduation_year,
            'graduation_date'  => $this->graduation_date?->toDateString(),
            'ipk'              => $this->ipk ? (float) $this->ipk : null,
            'thesis_title'     => $this->thesis_title,
            'photo'            => $this->photo,

            // Status pekerjaan
            'is_employed'           => $this->is_employed,
            'employment_status'     => $this->employment_status,
            'waiting_period_months' => $this->waiting_period_months,

            // Relasi — hanya dimuat jika eager-loaded (cegah N+1)
            'study_program' => $this->whenLoaded('studyProgram', fn () => [
                'id'           => $this->studyProgram->id,
                'code'         => $this->studyProgram->code,
                'name'         => $this->studyProgram->name,
                'degree_level' => $this->studyProgram->degree_level,
                'faculty'      => $this->studyProgram->relationLoaded('faculty') ? [
                    'id'   => $this->studyProgram->faculty->id,
                    'code' => $this->studyProgram->faculty->code,
                    'name' => $this->studyProgram->faculty->name,
                ] : null,
            ]),

            'user' => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
                'role'  => $this->user->role,
            ]),

            'employment_histories' => $this->whenLoaded(
                'employmentHistories',
                fn () => AlumniEmploymentHistoryResource::collection($this->employmentHistories)
            ),

            // Audit
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
