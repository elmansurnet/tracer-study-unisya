<?php

namespace App\Repositories;

use App\Models\ProfessionCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProfessionCategoryRepository
{
    public function __construct(protected ProfessionCategory $model) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->withCount('professions')
            ->when(isset($filters['search']), fn ($q) =>
                $q->where('name', 'like', "%{$filters['search']}%"))
            ->when(isset($filters['is_active']), fn ($q) =>
                $q->where('is_active', $filters['is_active']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?ProfessionCategory
    {
        return $this->model->withCount('professions')->find($id);
    }

    public function findByName(string $name, ?string $exceptId = null): ?ProfessionCategory
    {
        return $this->model
            ->where('name', $name)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->first();
    }

    public function create(array $data): ProfessionCategory
    {
        return $this->model->create($data);
    }

    public function update(ProfessionCategory $category, array $data): ProfessionCategory
    {
        $category->update($data);
        return $category->fresh();
    }

    public function softDelete(ProfessionCategory $category, string $deletedBy): bool
    {
        $category->deleted_by = $deletedBy;
        $category->save();
        return (bool) $category->delete();
    }

    public function restore(string $id): ?ProfessionCategory
    {
        $category = $this->model->withTrashed()->find($id);
        if ($category) {
            $category->deleted_by = null;
            $category->save();
            $category->restore();
        }
        return $category;
    }

    public function allActive(): Collection
    {
        return $this->model
            ->where('is_active', 1)
            ->withCount('professions')
            ->orderBy('name')
            ->get();
    }
}
