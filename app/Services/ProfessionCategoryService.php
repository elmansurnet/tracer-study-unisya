<?php

namespace App\Services;

use App\Models\ProfessionCategory;
use App\Models\User;
use App\Repositories\ProfessionCategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfessionCategoryService
{
    public function __construct(
        protected ProfessionCategoryRepository $categoryRepository,
        protected AuditService $auditService
    ) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->categoryRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): ProfessionCategory
    {
        $category = $this->categoryRepository->findById($id);
        if (! $category) {
            abort(404, 'Kategori profesi tidak ditemukan.');
        }
        return $category;
    }

    public function create(array $validated, User $actor): ProfessionCategory
    {
        $existing = $this->categoryRepository->findByName($validated['name']);
        if ($existing) {
            throw ValidationException::withMessages([
                'name' => ['Nama kategori profesi sudah digunakan.'],
            ]);
        }

        $category = $this->categoryRepository->create([
            'id'          => Str::ulid(),
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $validated['is_active'] ?? 1,
            'created_by'  => $actor->id,
            'updated_by'  => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $category,
            newValues: $category->toArray(),
            actor: $actor
        );

        return $category;
    }

    public function update(ProfessionCategory $category, array $validated, User $actor): ProfessionCategory
    {
        if (isset($validated['name'])) {
            $conflict = $this->categoryRepository->findByName($validated['name'], $category->id);
            if ($conflict) {
                throw ValidationException::withMessages([
                    'name' => ['Nama kategori profesi sudah digunakan.'],
                ]);
            }
        }

        $oldValues = $category->toArray();

        $updateData = array_filter([
            'name'        => $validated['name'] ?? null,
            'description' => array_key_exists('description', $validated) ? $validated['description'] : null,
            'is_active'   => $validated['is_active'] ?? null,
            'updated_by'  => $actor->id,
        ], fn ($v) => $v !== null);

        $updated = $this->categoryRepository->update($category, $updateData);

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $updated->toArray(),
            actor: $actor
        );

        return $updated;
    }

    public function delete(ProfessionCategory $category, User $actor): void
    {
        if ($category->professions()->exists()) {
            abort(422, 'Kategori profesi tidak dapat dihapus karena masih memiliki profesi.');
        }

        $this->auditService->log(
            event: 'deleted',
            auditable: $category,
            oldValues: $category->toArray(),
            actor: $actor
        );

        $this->categoryRepository->softDelete($category, $actor->id);
    }

    public function restore(string $id, User $actor): ProfessionCategory
    {
        $category = $this->categoryRepository->restore($id);
        if (! $category) {
            abort(404, 'Kategori profesi tidak ditemukan atau sudah aktif.');
        }

        $this->auditService->log(
            event: 'restored',
            auditable: $category,
            newValues: $category->toArray(),
            actor: $actor
        );

        return $category;
    }

    public function allActive(): \Illuminate\Support\Collection
    {
        return $this->categoryRepository->allActive();
    }
}
