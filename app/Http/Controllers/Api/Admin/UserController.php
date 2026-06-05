<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    /**
     * GET /api/v1/admin/users
     * Daftar pengguna dengan filter, sorting, dan paginasi.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $users = $this->userService->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only(['search', 'role', 'is_active']),
            sortBy:  $request->input('sort_by', 'created_at'),
            sortDir: $request->input('sort_dir', 'desc')
        );

        return UserResource::collection($users);
    }

    /**
     * POST /api/v1/admin/users
     * Buat akun alumni baru.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', \App\Models\User::class);

        $user = $this->userService->create(
            validated: $request->validated(),
            actor: $request->user()
        );

        return response()->json([
            'message' => 'Akun alumni berhasil dibuat.',
            'data'    => new UserResource($user),
        ], 201);
    }

    /**
     * GET /api/v1/admin/users/{id}
     * Detail satu pengguna.
     */
    public function show(string $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        $this->authorize('view', $user);

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    /**
     * PUT /api/v1/admin/users/{id}
     * Update data pengguna.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        $this->authorize('update', $user);

        $updated = $this->userService->update(
            user:      $user,
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Data pengguna berhasil diperbarui.',
            'data'    => new UserResource($updated),
        ]);
    }

    /**
     * DELETE /api/v1/admin/users/{id}
     * Soft delete pengguna.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        $this->authorize('delete', $user);

        $this->userService->delete($user, $request->user());

        return response()->json([
            'message' => 'Akun pengguna berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/users/{id}/toggle-active
     * Toggle status aktif pengguna.
     */
    public function toggleActive(Request $request, string $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        $this->authorize('update', $user);

        $updated = $this->userService->toggleActive($user, $request->user());

        return response()->json([
            'message' => $updated->is_active
                ? 'Akun berhasil diaktifkan.'
                : 'Akun berhasil dinonaktifkan.',
            'data'    => new UserResource($updated),
        ]);
    }

    /**
     * POST /api/v1/admin/users/{id}/reset-password
     * Reset password alumni.
     */
    public function resetPassword(ResetPasswordRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        $this->authorize('resetPassword', $user);

        $this->userService->resetPassword(
            user:        $user,
            newPassword: $request->validated('password'),
            actor:       $request->user()
        );

        return response()->json([
            'message' => 'Kata sandi berhasil direset.',
        ]);
    }
}
