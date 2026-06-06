<?php

namespace App\Repositories;

use App\Models\Profession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProfessionRepository
{
    public function __construct(protected Profession $model) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->with('category:id,name')
            ->when(isset($filters['search']), fn ($q) =>
                $q->where('name', 'like', "%{$filters['search']}%"))
            ->when(isset($filters['is_active']), fn ($q) =>
                $q->where('is_active', $filters['is_active']))
            ->when(isset($filters['profession_category_id']), fn ($q) =>
                $q->where('profession_category_id', $filters['profession_category_id']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?Profession
    {
        return $this->model->with('category:id,name')->find($id);
    }

    public function create(array $data): Profession
    {
        return $this->model->create($data);
    }

    public function update(Profession $profession, array $data): Profession
    {
        $profession->update($data);
        return $profession->fresh(['category']);
    }

    public function softDelete(Profession $profession, string $deletedBy): bool
    {
        $profession->deleted_by = $deletedBy;
        $profession->save();
        return (bool) $profession->delete();
    }

    public function restore(string $id): ?Profession
    {
        $profession = $this->model->withTrashed()->find($id);
        if ($profession) {
            $profession->deleted_by = null;
            $profession->save();
            $profession->restore();
        }
        return $profession;
    }

    public function allActive(): Collection
    {
        return $this->model
            ->where('is_active', 1)
            ->with('category:id,name')
            ->orderBy('name')
            ->get();
    }
}
