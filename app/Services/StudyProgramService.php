<?php

namespace App\Services;

use App\Models\StudyProgram;
use App\Models\User;
use App\Repositories\FacultyRepository;
use App\Repositories\StudyProgramRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudyProgramService
{
    public function __construct(
        protected StudyProgramRepository $studyProgramRepository,
        protected FacultyRepository $facultyRepository,
        protected AuditService $auditService
    ) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->studyProgramRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): StudyProgram
    {
        $sp = $this->studyProgramRepository->findById($id);
        if (! $sp) {
            abort(404, 'Program studi tidak ditemukan.');
        }
        return $sp;
    }

    public function create(array $validated, User $actor): StudyProgram
    {
        $faculty = $this->facultyRepository->findById($validated['faculty_id']);
        if (! $faculty) {
            throw ValidationException::withMessages([
                'faculty_id' => ['Fakultas tidak ditemukan.'],
            ]);
        }

        $existing = $this->studyProgramRepository->findByCode($validated['code']);
        if ($existing) {
            throw ValidationException::withMessages([
                'code' => ['Kode program studi sudah digunakan.'],
            ]);
        }

        $sp = $this->studyProgramRepository->create([
            'id'           => Str::ulid(),
            'faculty_id'   => $validated['faculty_id'],
            'code'         => strtoupper(trim($validated['code'])),
            'name'         => $validated['name'],
            'degree_level' => $validated['degree_level'],
            'description'  => $validated['description'] ?? null,
            'is_active'    => $validated['is_active'] ?? 1,
            'created_by'   => $actor->id,
            'updated_by'   => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $sp,
            newValues: $sp->toArray(),
            actor: $actor
        );

        return $sp;
    }

    public function update(StudyProgram $sp, array $validated, User $actor): StudyProgram
    {
        if (isset($validated['faculty_id'])) {
            $faculty = $this->facultyRepository->findById($validated['faculty_id']);
            if (! $faculty) {
                throw ValidationException::withMessages([
                    'faculty_id' => ['Fakultas tidak ditemukan.'],
                ]);
            }
        }

        if (isset($validated['code'])) {
            $conflict = $this->studyProgramRepository->findByCode($validated['code'], $sp->id);
            if ($conflict) {
                throw ValidationException::withMessages([
                    'code' => ['Kode program studi sudah digunakan.'],
                ]);
            }
        }

        $oldValues = $sp->toArray();

        $updateData = array_filter([
            'faculty_id'   => $validated['faculty_id'] ?? null,
            'code'         => isset($validated['code']) ? strtoupper(trim($validated['code'])) : null,
            'name'         => $validated['name'] ?? null,
            'degree_level' => $validated['degree_level'] ?? null,
            'description'  => array_key_exists('description', $validated) ? $validated['description'] : null,
            'is_active'    => $validated['is_active'] ?? null,
            'updated_by'   => $actor->id,
        ], fn ($v) => $v !== null);

        $updated = $this->studyProgramRepository->update($sp, $updateData);

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    public function delete(StudyProgram $sp, User $actor): void
    {
        $this->auditService->log(
            event: 'deleted',
            auditable: $sp,
            oldValues: $sp->toArray(),
            actor: $actor
        );

        $this->studyProgramRepository->softDelete($sp, $actor->id);
    }

    public function restore(string $id, User $actor): StudyProgram
    {
        $sp = $this->studyProgramRepository->restore($id);
        if (! $sp) {
            abort(404, 'Program studi tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $sp,
            newValues: $sp->toArray(),
            actor: $actor
        );

        return $sp;
    }

    public function byFaculty(string $facultyId): \Illuminate\Support\Collection
    {
        return $this->studyProgramRepository->byFaculty($facultyId);
    }

    public function allActive(): \Illuminate\Support\Collection
    {
        return $this->studyProgramRepository->allActive();
    }
}
