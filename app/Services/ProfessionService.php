<?php

namespace App\Services;

use App\Models\Profession;
use App\Models\User;
use App\Repositories\ProfessionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfessionService
{
    public function __construct(
        protected ProfessionRepository $professionRepository,
        protected AuditService $auditService
    ) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->professionRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): Profession
    {
        $profession = $this->professionRepository->findById($id);
        if (! $profession) {
            abort(404, 'Profesi tidak ditemukan.');
        }
        return $profession;
    }

    public function create(array $validated, User $actor): Profession
    {
        $existing = $this->professionRepository->findByNameInCategory(
            $validated['name'],
            $validated['profession_category_id']
        );
        if ($existing) {
            throw ValidationException::withMessages([
                'name' => ['Nama profesi sudah ada dalam kategori ini.'],
            ]);
        }

        $profession = $this->professionRepository->create([
            'id'                     => Str::ulid(),
            'profession_category_id' => $validated['profession_category_id'],
            'name'                   => $validated['name'],
            'description'            => $validated['description'] ?? null,
            'is_active'              => $validated['is_active'] ?? 1,
            'created_by'             => $actor->id,
            'updated_by'             => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $profession,
            newValues: $profession->toArray(),
            actor: $actor
        );

        return $profession->load('category');
    }

    public function update(Profession $profession, array $validated, User $actor): Profession
    {
        $targetCategoryId = $validated['profession_category_id'] ?? $profession->profession_category_id;
        $targetName       = $validated['name'] ?? $profession->name;

        if (isset($validated['name']) || isset($validated['profession_category_id'])) {
            $conflict = $this->professionRepository->findByNameInCategory(
                $targetName,
                $targetCategoryId,
                $profession->id
            );
            if ($conflict) {
                throw ValidationException::withMessages([
                    'name' => ['Nama profesi sudah ada dalam kategori ini.'],
                ]);
            }
        }

        $oldValues = $profession->toArray();

        $updateData = array_filter([
            'profession_category_id' => $validated['profession_category_id'] ?? null,
            'name'                   => $validated['name'] ?? null,
            'description'            => array_key_exists('description', $validated) ? $validated['description'] : null,
            'is_active'              => $validated['is_active'] ?? null,
            'updated_by'             => $actor->id,
        ], fn ($v) => $v !== null);

        $updated = $this->professionRepository->update($profession, $updateData);

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    public function delete(Profession $profession, User $actor): void
    {
        $this->auditService->log(
            event: 'deleted',
            auditable: $profession,
            oldValues: $profession->toArray(),
            actor: $actor
        );

        $this->professionRepository->softDelete($profession, $actor->id);
    }

    public function restore(string $id, User $actor): Profession
    {
        $profession = $this->professionRepository->restore($id);
        if (! $profession) {
            abort(404, 'Profesi tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $profession,
            newValues: $profession->toArray(),
            actor: $actor
        );

        return $profession;
    }

    public function allActive(?string $categoryId = null): \Illuminate\Support\Collection
    {
        return $this->professionRepository->allActive($categoryId);
    }
}
