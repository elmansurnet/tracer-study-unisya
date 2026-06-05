<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository
{
    public function __construct(protected User $model) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortDir = 'desc'
    ): LengthAwarePaginator {
        return $this->model
            ->newQuery()
            ->when(isset($filters['search']), fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$filters['search']}%")
                       ->orWhere('email', 'like', "%{$filters['search']}%")
                ))
            ->when(isset($filters['role']), fn ($q) =>
                $q->where('role', $filters['role']))
            ->when(isset($filters['is_active']), fn ($q) =>
                $q->where('is_active', $filters['is_active']))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findById(string $id): ?User
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function softDelete(User $user, string $deletedBy): bool
    {
        $user->deleted_by = $deletedBy;
        $user->save();
        return (bool) $user->delete();
    }

    public function restore(string $id): ?User
    {
        $user = $this->model->withTrashed()->find($id);
        if ($user) {
            $user->deleted_by = null;
            $user->save();
            $user->restore();
        }
        return $user;
    }

    public function allActive(): Collection
    {
        return $this->model->where('is_active', 1)->orderBy('name')->get();
    }
}
