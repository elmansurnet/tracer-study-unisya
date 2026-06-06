<?php

namespace App\Repositories;

use App\Models\AnswerType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AnswerTypeRepository
{
    // ─── Read ──────────────────────────────────────────────────────────────────

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AnswerType::query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function all(bool $onlyActive = false): Collection
    {
        $query = AnswerType::query();

        if ($onlyActive) {
            $query->active();
        }

        return $query->orderBy('name')->get();
    }

    public function findById(string $id): ?AnswerType
    {
        return AnswerType::find($id);
    }

    public function findByIdOrFail(string $id): AnswerType
    {
        return AnswerType::findOrFail($id);
    }

    public function findByCode(string $code): ?AnswerType
    {
        return AnswerType::where('code', $code)->first();
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(array $data): AnswerType
    {
        return AnswerType::create($data);
    }

    public function update(AnswerType $answerType, array $data): AnswerType
    {
        $answerType->update($data);
        return $answerType->fresh();
    }

    public function delete(AnswerType $answerType): void
    {
        $answerType->delete();
    }
}
