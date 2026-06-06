<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\User;
use App\Repositories\AlumniRepository;
use App\Repositories\StudyProgramRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AlumniService
{
    public function __construct(
        protected AlumniRepository        $alumniRepository,
        protected StudyProgramRepository  $studyProgramRepository,
        protected AuditService            $auditService
    ) {}

    // ─── Read ──────────────────────────────────────────────────────────────

    public function paginate(
        int    $perPage  = 15,
        array  $filters  = [],
        string $sortBy   = 'name',
        string $sortDir  = 'asc'
    ): LengthAwarePaginator {
        return $this->alumniRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): Alumni
    {
        $alumni = $this->alumniRepository->findById($id);
        if (! $alumni) {
            abort(404, 'Alumni tidak ditemukan.');
        }
        return $alumni;
    }

    public function findOrFailWithTrashed(string $id): Alumni
    {
        $alumni = Alumni::withTrashed()->find($id);
        if (! $alumni) {
            abort(404, 'Alumni tidak ditemukan.');
        }
        return $alumni;
    }

    public function byStudyProgram(string $studyProgramId, ?int $graduationYear = null): Collection
    {
        return $this->alumniRepository->byStudyProgram($studyProgramId, $graduationYear);
    }

    public function byGraduationYear(int $year): Collection
    {
        return $this->alumniRepository->byGraduationYear($year);
    }

    public function countByEmploymentStatus(?int $graduationYear = null): array
    {
        return $this->alumniRepository->countByEmploymentStatus($graduationYear);
    }

    public function graduationYears(): Collection
    {
        return $this->alumniRepository->graduationYears();
    }

    // ─── Write ────────────────────────────────────────────────────────────

    public function create(array $validated, User $actor): Alumni
    {
        // 1. Validasi NIM unik
        if ($this->alumniRepository->findByNim($validated['nim'])) {
            throw ValidationException::withMessages([
                'nim' => ['NIM sudah terdaftar.'],
            ]);
        }

        // 2. Validasi study_program exists
        $sp = $this->studyProgramRepository->findById($validated['study_program_id']);
        if (! $sp) {
            throw ValidationException::withMessages([
                'study_program_id' => ['Program studi tidak ditemukan.'],
            ]);
        }

        // 3. Resolve user_id by email jika dikirim
        $userId = $validated['user_id'] ?? null;
        if (! $userId && isset($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
            $userId = $user?->id;
        }

        $alumni = $this->alumniRepository->create([
            'id'                     => Str::ulid(),
            'user_id'                => $userId,
            'study_program_id'       => $validated['study_program_id'],
            'nim'                    => $validated['nim'],
            'name'                   => $validated['name'],
            'gender'                 => $validated['gender'],
            'birth_place'            => $validated['birth_place']            ?? null,
            'birth_date'             => $validated['birth_date']             ?? null,
            'address'                => $validated['address']                ?? null,
            'city'                   => $validated['city']                   ?? null,
            'province'               => $validated['province']               ?? null,
            'postal_code'            => $validated['postal_code']            ?? null,
            'phone'                  => $validated['phone']                  ?? null,
            'email'                  => $validated['email']                  ?? null,
            'graduation_year'        => $validated['graduation_year'],
            'graduation_date'        => $validated['graduation_date']        ?? null,
            'ipk'                    => $validated['ipk']                    ?? null,
            'thesis_title'           => $validated['thesis_title']           ?? null,
            'photo'                  => $validated['photo']                  ?? null,
            'is_employed'            => $validated['is_employed']            ?? false,
            'employment_status'      => $validated['employment_status']      ?? null,
            'waiting_period_months'  => $validated['waiting_period_months']  ?? null,
            'created_by'             => $actor->id,
            'updated_by'             => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $alumni,
            newValues: $alumni->toArray(),
            actor: $actor
        );

        return $alumni;
    }

    public function update(Alumni $alumni, array $validated, User $actor): Alumni
    {
        // Validasi NIM unik (kecuali milik sendiri)
        if (isset($validated['nim'])) {
            $conflict = $this->alumniRepository->findByNim($validated['nim'], $alumni->id);
            if ($conflict) {
                throw ValidationException::withMessages([
                    'nim' => ['NIM sudah digunakan alumni lain.'],
                ]);
            }
        }

        // Validasi study_program exists jika diganti
        if (isset($validated['study_program_id'])) {
            $sp = $this->studyProgramRepository->findById($validated['study_program_id']);
            if (! $sp) {
                throw ValidationException::withMessages([
                    'study_program_id' => ['Program studi tidak ditemukan.'],
                ]);
            }
        }

        $oldValues = $alumni->toArray();

        // Partial update: hanya field yang dikirim
        $updateData = [];
        $scalarFields = [
            'study_program_id', 'nim', 'name', 'gender', 'birth_place',
            'birth_date', 'address', 'city', 'province', 'postal_code',
            'phone', 'email', 'graduation_year', 'graduation_date',
            'ipk', 'thesis_title', 'photo', 'is_employed',
            'employment_status', 'waiting_period_months', 'user_id',
        ];

        foreach ($scalarFields as $field) {
            if (array_key_exists($field, $validated)) {
                $updateData[$field] = $validated[$field];
            }
        }

        $updateData['updated_by'] = $actor->id;

        $updated = $this->alumniRepository->update($alumni, $updateData);

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    /**
     * Update employment_status + is_employed secara atomik.
     * Digunakan oleh alumni self-service maupun admin.
     */
    public function updateEmploymentStatus(
        Alumni $alumni,
        string $status,
        bool   $isEmployed,
        ?int   $waitingPeriodMonths,
        User   $actor
    ): Alumni {
        $oldValues = $alumni->toArray();

        $updated = $this->alumniRepository->update($alumni, [
            'employment_status'     => $status,
            'is_employed'           => $isEmployed,
            'waiting_period_months' => $waitingPeriodMonths,
            'updated_by'            => $actor->id,
        ]);

        $this->auditService->log(
            event: 'employment_status_updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    public function delete(Alumni $alumni, User $actor): void
    {
        // Cegah hapus jika masih ada tracer study terkait
        if ($alumni->tracerStudies()->exists()) {
            abort(422, 'Alumni tidak dapat dihapus karena memiliki data tracer study.');
        }

        $this->auditService->log(
            event: 'deleted',
            auditable: $alumni,
            oldValues: $alumni->toArray(),
            actor: $actor
        );

        $this->alumniRepository->softDelete($alumni, $actor->id);
    }

    public function restore(string $id, User $actor): Alumni
    {
        $alumni = $this->alumniRepository->restore($id);
        if (! $alumni) {
            abort(404, 'Alumni tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $alumni,
            newValues: $alumni->toArray(),
            actor: $actor
        );

        return $alumni;
    }
}
