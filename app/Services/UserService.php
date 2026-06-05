<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuditService $auditService
    ) {}

    public function paginate(
        int $perPage = 15,
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortDir = 'desc'
    ): LengthAwarePaginator {
        return $this->userRepository->paginate($perPage, $filters, $sortBy, $sortDir);
    }

    public function findOrFail(string $id): User
    {
        $user = $this->userRepository->findById($id);
        if (! $user) {
            abort(404, 'Pengguna tidak ditemukan.');
        }
        return $user;
    }

    /**
     * Buat akun alumni baru oleh Super Admin.
     */
    public function create(array $validated, User $actor): User
    {
        $existing = $this->userRepository->findByEmail($validated['email']);
        if ($existing) {
            throw ValidationException::withMessages([
                'email' => ['Email sudah digunakan oleh pengguna lain.'],
            ]);
        }

        $password = $validated['password'] ?? Str::random(12);

        $user = $this->userRepository->create([
            'id'         => Str::ulid(),
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($password),
            'role'       => 'alumni',
            'is_active'  => $validated['is_active'] ?? 1,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        $this->auditService->log(
            event: 'created',
            auditable: $user,
            newValues: $user->toArray(),
            actor: $actor
        );

        return $user;
    }

    /**
     * Update data pengguna.
     */
    public function update(User $user, array $validated, User $actor): User
    {
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $existing = $this->userRepository->findByEmail($validated['email']);
            if ($existing) {
                throw ValidationException::withMessages([
                    'email' => ['Email sudah digunakan oleh pengguna lain.'],
                ]);
            }
        }

        $oldValues = $user->toArray();

        $updateData = array_filter([
            'name'       => $validated['name'] ?? null,
            'email'      => $validated['email'] ?? null,
            'phone'      => $validated['phone'] ?? null,
            'is_active'  => $validated['is_active'] ?? null,
            'updated_by' => $actor->id,
        ], fn ($v) => $v !== null);

        $updated = $this->userRepository->update($user, $updateData);

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
     * Reset password alumni oleh Super Admin.
     */
    public function resetPassword(User $user, string $newPassword, User $actor): void
    {
        $this->userRepository->update($user, [
            'password'   => Hash::make($newPassword),
            'updated_by' => $actor->id,
        ]);

        $this->auditService->log(
            event: 'updated',
            auditable: $user,
            newValues: ['password' => '***RESET***'],
            actor: $actor
        );
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleActive(User $user, User $actor): User
    {
        if ($user->id === $actor->id) {
            abort(422, 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $oldValues = ['is_active' => $user->is_active];
        $updated   = $this->userRepository->update($user, [
            'is_active'  => ! $user->is_active,
            'updated_by' => $actor->id,
        ]);

        $this->auditService->log(
            event: 'updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: ['is_active' => $updated->is_active],
            actor: $actor
        );

        return $updated;
    }

    /**
     * Soft delete pengguna.
     */
    public function delete(User $user, User $actor): void
    {
        if ($user->id === $actor->id) {
            abort(422, 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->role === 'super_admin') {
            abort(422, 'Akun Super Admin tidak dapat dihapus.');
        }

        $this->auditService->log(
            event: 'deleted',
            auditable: $user,
            oldValues: $user->toArray(),
            actor: $actor
        );

        $this->userRepository->softDelete($user, $actor->id);
    }
}
