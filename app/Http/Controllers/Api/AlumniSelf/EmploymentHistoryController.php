<?php

namespace App\Http\Controllers\Api\AlumniSelf;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlumniSelf\StoreEmploymentHistoryRequest;
use App\Http\Requests\AlumniSelf\UpdateEmploymentHistoryRequest;
use App\Http\Resources\AlumniEmploymentHistoryResource;
use App\Models\AlumniEmploymentHistory;
use App\Services\AlumniEmploymentHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmploymentHistoryController extends Controller
{
    public function __construct(
        protected AlumniEmploymentHistoryService $historyService
    ) {}

    /**
     * GET /api/v1/alumni/employment-histories
     * Daftar semua riwayat pekerjaan alumni yang sedang login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $this->authorize('viewAny', [AlumniEmploymentHistory::class, $alumni]);

        $histories = $this->historyService->paginate(
            alumniId: $alumni->id,
            perPage:  (int) $request->input('per_page', 15),
            filters:  $request->only(['is_current']),
            sortBy:   'start_date',
            sortDir:  'desc'
        );

        return AlumniEmploymentHistoryResource::collection($histories);
    }

    /**
     * POST /api/v1/alumni/employment-histories
     * Alumni menambahkan riwayat pekerjaan baru.
     */
    public function store(StoreEmploymentHistoryRequest $request): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $this->authorize('create', [AlumniEmploymentHistory::class, $alumni]);

        $history = $this->historyService->create(
            validated: $request->validated(),
            alumni:    $alumni,
            actor:     $user
        );

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil ditambahkan.',
            'data'    => new AlumniEmploymentHistoryResource(
                $history->load(['institution', 'profession.category'])
            ),
        ], 201);
    }

    /**
     * GET /api/v1/alumni/employment-histories/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $history = $this->historyService->findOrFail($id);

        // Pastikan riwayat milik alumni yang sedang login
        abort_unless((string) $history->alumni_id === (string) $alumni->id, 403, 'Akses ditolak.');

        $this->authorize('view', $history);

        return response()->json([
            'data' => new AlumniEmploymentHistoryResource(
                $history->load(['institution', 'profession.category'])
            ),
        ]);
    }

    /**
     * PUT /api/v1/alumni/employment-histories/{id}
     * Alumni memperbarui riwayat pekerjaan miliknya.
     */
    public function update(UpdateEmploymentHistoryRequest $request, string $id): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $history = $this->historyService->findOrFail($id);

        abort_unless((string) $history->alumni_id === (string) $alumni->id, 403, 'Akses ditolak.');

        $this->authorize('update', $history);

        $updated = $this->historyService->update(
            history:   $history,
            validated: $request->validated(),
            actor:     $user
        );

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil diperbarui.',
            'data'    => new AlumniEmploymentHistoryResource(
                $updated->load(['institution', 'profession.category'])
            ),
        ]);
    }

    /**
     * DELETE /api/v1/alumni/employment-histories/{id}
     * Alumni menghapus riwayat pekerjaan miliknya.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $history = $this->historyService->findOrFail($id);

        abort_unless((string) $history->alumni_id === (string) $alumni->id, 403, 'Akses ditolak.');

        $this->authorize('delete', $history);

        $this->historyService->delete($history, $user);

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/alumni/employment-histories/{id}/restore
     * Alumni memulihkan riwayat pekerjaan yang pernah dihapus.
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $user   = $request->user();
        $alumni = $user->alumni;

        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        // Cek kepemilikan sebelum restore (pakai withTrashed)
        $history = AlumniEmploymentHistory::withTrashed()->find($id);
        abort_unless($history !== null, 404, 'Riwayat pekerjaan tidak ditemukan.');
        abort_unless((string) $history->alumni_id === (string) $alumni->id, 403, 'Akses ditolak.');

        $this->authorize('restore', $history);

        $restored = $this->historyService->restore($id, $user);

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil dipulihkan.',
            'data'    => new AlumniEmploymentHistoryResource(
                $restored->load(['institution', 'profession.category'])
            ),
        ]);
    }
}
