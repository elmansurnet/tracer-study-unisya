<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAlumniEmploymentHistoryRequest;
use App\Http\Requests\Admin\UpdateAlumniEmploymentHistoryRequest;
use App\Http\Resources\AlumniEmploymentHistoryResource;
use App\Models\AlumniEmploymentHistory;
use App\Services\AlumniEmploymentHistoryService;
use App\Services\AlumniService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AlumniEmploymentHistoryController extends Controller
{
    public function __construct(
        protected AlumniEmploymentHistoryService $historyService,
        protected AlumniService                  $alumniService
    ) {}

    /**
     * GET /api/v1/admin/alumni/{alumniId}/employment-histories
     */
    public function index(Request $request, string $alumniId): AnonymousResourceCollection
    {
        $alumni = $this->alumniService->findOrFail($alumniId);
        $this->authorize('view', $alumni);

        $histories = $this->historyService->paginate(
            alumniId: $alumni->id,
            perPage:  (int) $request->input('per_page', 15),
            filters:  $request->only(['search', 'is_current', 'with_trashed']),
            sortBy:   $request->input('sort_by', 'start_date'),
            sortDir:  $request->input('sort_dir', 'desc')
        );

        return AlumniEmploymentHistoryResource::collection($histories);
    }

    /**
     * POST /api/v1/admin/alumni/{alumniId}/employment-histories
     */
    public function store(StoreAlumniEmploymentHistoryRequest $request, string $alumniId): JsonResponse
    {
        $alumni = $this->alumniService->findOrFail($alumniId);
        $this->authorize('create', [AlumniEmploymentHistory::class, $alumni]);

        $history = $this->historyService->create(
            validated: $request->validated(),
            alumni:    $alumni,
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil ditambahkan.',
            'data'    => new AlumniEmploymentHistoryResource(
                $history->load(['institution', 'profession.category'])
            ),
        ], 201);
    }

    /**
     * GET /api/v1/admin/alumni/{alumniId}/employment-histories/{id}
     */
    public function show(string $alumniId, string $id): JsonResponse
    {
        $alumni  = $this->alumniService->findOrFail($alumniId);
        $history = $this->historyService->findOrFail($id);

        // Pastikan riwayat memang milik alumni ini
        abort_unless((string) $history->alumni_id === (string) $alumni->id, 404, 'Riwayat pekerjaan tidak ditemukan.');

        $this->authorize('view', $history);

        return response()->json([
            'data' => new AlumniEmploymentHistoryResource(
                $history->load(['institution', 'profession.category', 'alumni'])
            ),
        ]);
    }

    /**
     * PUT /api/v1/admin/alumni/{alumniId}/employment-histories/{id}
     */
    public function update(
        UpdateAlumniEmploymentHistoryRequest $request,
        string $alumniId,
        string $id
    ): JsonResponse {
        $alumni  = $this->alumniService->findOrFail($alumniId);
        $history = $this->historyService->findOrFail($id);

        abort_unless((string) $history->alumni_id === (string) $alumni->id, 404, 'Riwayat pekerjaan tidak ditemukan.');

        $this->authorize('update', $history);

        $updated = $this->historyService->update(
            history:   $history,
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil diperbarui.',
            'data'    => new AlumniEmploymentHistoryResource(
                $updated->load(['institution', 'profession.category'])
            ),
        ]);
    }

    /**
     * DELETE /api/v1/admin/alumni/{alumniId}/employment-histories/{id}
     */
    public function destroy(Request $request, string $alumniId, string $id): JsonResponse
    {
        $alumni  = $this->alumniService->findOrFail($alumniId);
        $history = $this->historyService->findOrFail($id);

        abort_unless((string) $history->alumni_id === (string) $alumni->id, 404, 'Riwayat pekerjaan tidak ditemukan.');

        $this->authorize('delete', $history);

        $this->historyService->delete($history, $request->user());

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/alumni/{alumniId}/employment-histories/{id}/restore
     */
    public function restore(Request $request, string $alumniId, string $id): JsonResponse
    {
        $this->authorize('restore', AlumniEmploymentHistory::class);

        $history = $this->historyService->restore($id, $request->user());

        // Pastikan riwayat memang milik alumni ini setelah restore
        abort_unless((string) $history->alumni_id === $alumniId, 404, 'Riwayat pekerjaan tidak ditemukan.');

        return response()->json([
            'message' => 'Riwayat pekerjaan berhasil dipulihkan.',
            'data'    => new AlumniEmploymentHistoryResource(
                $history->load(['institution', 'profession.category'])
            ),
        ]);
    }
}
