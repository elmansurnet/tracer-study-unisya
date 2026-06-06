<?php

namespace App\Http\Controllers\Api\AlumniSelf;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlumniSelf\StoreAlumniRequestRequest;
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
     * GET /api/v1/alumni/requests
     * Daftar semua permohonan milik alumni yang sedang login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $alumni = $request->user()->alumni;
        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $this->authorize('viewAny', AlumniRequest::class);

        $requests = $this->requestService->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: array_merge(
                $request->only(['status', 'type']),
                ['alumni_id' => $alumni->id]   // hard-scope ke alumni sendiri
            ),
            sortBy:  $request->input('sort_by', 'created_at'),
            sortDir: $request->input('sort_dir', 'desc')
        );

        return AlumniRequestResource::collection($requests);
    }

    /**
     * POST /api/v1/alumni/requests
     * Alumni mengajukan permohonan perubahan data.
     */
    public function store(StoreAlumniRequestRequest $request): JsonResponse
    {
        $alumni = $request->user()->alumni;
        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $this->authorize('create', AlumniRequest::class);

        // alumni_id di-inject dari sesi, tidak dari body (anti mass-assignment)
        $alumniRequest = $this->requestService->create(
            validated: array_merge(
                $request->validated(),
                ['alumni_id' => $alumni->id]
            ),
            actor: $request->user()
        );

        return response()->json([
            'message' => 'Permohonan berhasil diajukan.',
            'data'    => new AlumniRequestResource(
                $alumniRequest->load(['alumni.studyProgram'])
            ),
        ], 201);
    }

    /**
     * GET /api/v1/alumni/requests/{id}
     * Detail satu permohonan milik alumni yang sedang login.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $alumni = $request->user()->alumni;
        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $alumniRequest = $this->requestService->findOrFail($id);

        // Pastikan permohonan milik alumni yang sedang login
        abort_unless(
            (string) $alumniRequest->alumni_id === (string) $alumni->id,
            403,
            'Akses ditolak.'
        );

        $this->authorize('view', $alumniRequest);

        return response()->json([
            'data' => new AlumniRequestResource(
                $alumniRequest->load(['alumni.studyProgram', 'reviewer'])
            ),
        ]);
    }

    /**
     * DELETE /api/v1/alumni/requests/{id}
     * Alumni membatalkan permohonan yang masih menunggu.
     * Menggunakan method 'cancel' (alias destroy) agar lebih deskriptif di sisi alumni.
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $alumni = $request->user()->alumni;
        abort_unless($alumni !== null, 404, 'Data alumni tidak ditemukan untuk akun ini.');

        $alumniRequest = $this->requestService->findOrFail($id);

        // Pastikan permohonan milik alumni yang sedang login
        abort_unless(
            (string) $alumniRequest->alumni_id === (string) $alumni->id,
            403,
            'Akses ditolak.'
        );

        // Alumni hanya boleh batalkan yang masih menunggu
        abort_unless(
            $alumniRequest->isPending(),
            422,
            'Hanya permohonan yang masih menunggu yang dapat dibatalkan.'
        );

        $this->authorize('delete', $alumniRequest);

        $this->requestService->delete($alumniRequest, $request->user());

        return response()->json([
            'message' => 'Permohonan berhasil dibatalkan.',
        ]);
    }
}
