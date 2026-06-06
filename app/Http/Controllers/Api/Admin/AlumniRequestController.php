<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewAlumniRequestRequest;
use App\Http\Requests\Admin\StoreAlumniRequestRequest;
use App\Http\Requests\Admin\UpdateAlumniRequestRequest;
use App\Http\Resources\AlumniRequestResource;
use App\Models\AlumniRequest;
use App\Services\AlumniRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AlumniRequestController extends Controller
{
    public function __construct(
        protected AlumniRequestService $requestService
    ) {}

    /**
     * GET /api/v1/admin/alumni-requests
     * Daftar semua permohonan dengan filter + paginasi.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', AlumniRequest::class);

        $requests = $this->requestService->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only([
                'alumni_id',
                'status',
                'type',
                'search',
                'study_program_id',
            ]),
            sortBy:  $request->input('sort_by', 'created_at'),
            sortDir: $request->input('sort_dir', 'desc')
        );

        return AlumniRequestResource::collection($requests);
    }

    /**
     * GET /api/v1/admin/alumni-requests/{id}
     * Detail satu permohonan.
     */
    public function show(string $id): JsonResponse
    {
        $alumniRequest = $this->requestService->findOrFail($id);
        $this->authorize('view', $alumniRequest);

        return response()->json([
            'data' => new AlumniRequestResource(
                $alumniRequest->load(['alumni.studyProgram.faculty', 'reviewer', 'creator', 'updater'])
            ),
        ]);
    }

    /**
     * POST /api/v1/admin/alumni-requests
     * Admin membuat permohonan atas nama alumni (opsional).
     */
    public function store(StoreAlumniRequestRequest $request): JsonResponse
    {
        $this->authorize('create', AlumniRequest::class);

        $alumniRequest = $this->requestService->create(
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Permohonan berhasil dibuat.',
            'data'    => new AlumniRequestResource(
                $alumniRequest->load(['alumni.studyProgram', 'creator'])
            ),
        ], 201);
    }

    /**
     * PUT /api/v1/admin/alumni-requests/{id}
     * Update permohonan (terbatas: new_value, reason) selama masih pending.
     */
    public function update(UpdateAlumniRequestRequest $request, string $id): JsonResponse
    {
        $alumniRequest = $this->requestService->findOrFail($id);
        $this->authorize('update', $alumniRequest);

        abort_unless(
            $alumniRequest->isPending(),
            422,
            'Hanya permohonan berstatus menunggu yang dapat diubah.'
        );

        $alumniRequest->update(array_merge(
            $request->validated(),
            ['updated_by' => $request->user()->id]
        ));

        return response()->json([
            'message' => 'Permohonan berhasil diperbarui.',
            'data'    => new AlumniRequestResource(
                $alumniRequest->fresh()->load(['alumni.studyProgram', 'updater'])
            ),
        ]);
    }

    /**
     * DELETE /api/v1/admin/alumni-requests/{id}
     * Soft-delete permohonan.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $alumniRequest = $this->requestService->findOrFail($id);
        $this->authorize('delete', $alumniRequest);

        $this->requestService->delete($alumniRequest, $request->user());

        return response()->json([
            'message' => 'Permohonan berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/alumni-requests/{id}/restore
     * Pulihkan permohonan yang dihapus.
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $this->authorize('restore', AlumniRequest::class);

        $alumniRequest = $this->requestService->restore($id, $request->user());

        return response()->json([
            'message' => 'Permohonan berhasil dipulihkan.',
            'data'    => new AlumniRequestResource(
                $alumniRequest->load(['alumni.studyProgram', 'reviewer'])
            ),
        ]);
    }

    /**
     * POST /api/v1/admin/alumni-requests/{id}/approve
     * Setujui permohonan dan terapkan perubahan ke data alumni.
     */
    public function approve(ReviewAlumniRequestRequest $request, string $id): JsonResponse
    {
        $alumniRequest = $this->requestService->findOrFail($id);
        $this->authorize('approve', $alumniRequest);

        $approved = $this->requestService->approve(
            alumniRequest: $alumniRequest,
            actor:         $request->user(),
            notes:         $request->input('review_notes')
        );

        return response()->json([
            'message' => 'Permohonan berhasil disetujui.',
            'data'    => new AlumniRequestResource(
                $approved->load(['alumni.studyProgram', 'reviewer'])
            ),
        ]);
    }

    /**
     * POST /api/v1/admin/alumni-requests/{id}/reject
     * Tolak permohonan.
     */
    public function reject(ReviewAlumniRequestRequest $request, string $id): JsonResponse
    {
        $alumniRequest = $this->requestService->findOrFail($id);
        $this->authorize('reject', $alumniRequest);

        $rejected = $this->requestService->reject(
            alumniRequest: $alumniRequest,
            actor:         $request->user(),
            notes:         $request->input('review_notes')
        );

        return response()->json([
            'message' => 'Permohonan berhasil ditolak.',
            'data'    => new AlumniRequestResource(
                $rejected->load(['alumni.studyProgram', 'reviewer'])
            ),
        ]);
    }

    /**
     * GET /api/v1/admin/alumni-requests/count-pending
     * Jumlah permohonan menunggu (untuk badge notifikasi sidebar admin).
     */
    public function countPending(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AlumniRequest::class);

        return response()->json([
            'data' => [
                'pending'          => $this->requestService->countPending(),
                'stats_by_status'  => $this->requestService->countByStatus(),
            ],
        ]);
    }
}
