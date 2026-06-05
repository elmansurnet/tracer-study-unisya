<?php

namespace App\Services;

use App\Models\Faculty;
use App\Models\User;
use App\Repositories\FacultyRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FacultyService
{
    public function __construct(
        protected FacultyRepository $facultyRepository,
        protected AuditService $auditService
    ) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->facultyRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): Faculty
    {
        $faculty = $this->facultyRepository->findById($id);
        if (! $faculty) {
            abort(404, 'Fakultas tidak ditemukan.');
        }
        return $faculty;
    }

    public function create(array $validated, User $actor): Faculty
    {
        $existing = $this->facultyRepository->findByCode($validated['code']);
        if ($existing) {
            throw ValidationException::withMessages([
                'code' => ['Kode fakultas sudah digunakan.'],
            ]);
        }

        $faculty = $this->facultyRepository->create([
            'id'          => Str::ulid(),
            'code'        => strtoupper(trim($validated['code'])),
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $validated['is_active'] ?? 1,
            'created_by'  => $actor->id,
            'updated_by'  => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $faculty,
            newValues: $faculty->toArray(),
            actor: $actor
        );

        return $faculty;
    }

    public function update(Faculty $faculty, array $validated, User $actor): Faculty
    {
        if (isset($validated['code'])) {
            $conflict = $this->facultyRepository->findByCode($validated['code'], $faculty->id);
            if ($conflict) {
                throw ValidationException::withMessages([
                    'code' => ['Kode fakultas sudah digunakan.'],
                ]);
            }
        }

        $oldValues = $faculty->toArray();

        $updateData = array_filter([
            'code'        => isset($validated['code']) ? strtoupper(trim($validated['code'])) : null,
            'name'        => $validated['name'] ?? null,
            'description' => array_key_exists('description', $validated) ? $validated['description'] : null,
            'is_active'   => $validated['is_active'] ?? null,
            'updated_by'  => $actor->id,
        ], fn ($v) => $v !== null);

        $updated = $this->facultyRepository->update($faculty, $updateData);

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    public function delete(Faculty $faculty, User $actor): void
    {
        if ($faculty->studyPrograms()->exists()) {
            abort(422, 'Fakultas tidak dapat dihapus karena masih memiliki program studi.');
        }

        $this->auditService->log(
            event: 'deleted',
            auditable: $faculty,
            oldValues: $faculty->toArray(),
            actor: $actor
        );

        $this->facultyRepository->softDelete($faculty, $actor->id);
    }

    public function restore(string $id, User $actor): Faculty
    {
        $faculty = $this->facultyRepository->restore($id);
        if (! $faculty) {
            abort(404, 'Fakultas tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $faculty,
            newValues: $faculty->toArray(),
            actor: $actor
        );

        return $faculty;
    }

    public function allActive(): \Illuminate\Support\Collection
    {
        return $this->facultyRepository->allActive();
    }
}
