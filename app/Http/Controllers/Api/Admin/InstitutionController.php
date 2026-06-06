<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInstitutionRequest;
use App\Http\Requests\Admin\UpdateInstitutionRequest;
use App\Http\Resources\InstitutionResource;
use App\Models\Institution;
use App\Services\InstitutionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InstitutionController extends Controller
{
    public function __construct(protected InstitutionService $service) {}

    /**
     * GET /api/v1/admin/institutions
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Institution::class);

        $institutions = $this->service->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only(['search', 'is_active', 'type']),
            sortBy:  $request->input('sort_by', 'name'),
            sortDir: $request->input('sort_dir', 'asc')
        );

        return InstitutionResource::collection($institutions);
    }

    /**
     * GET /api/v1/admin/institutions/all
     */
    public function all(): JsonResponse
    {
        $this->authorize('viewAny', Institution::class);

        return response()->json([
            'data' => InstitutionResource::collection($this->service->allActive()),
        ]);
    }

    /**
     * POST /api/v1/admin/institutions
     */
    public function store(StoreInstitutionRequest $request): JsonResponse
    {
        $this->authorize('create', Institution::class);

        $institution = $this->service->create(
            validated: $request->validated(),
            actor: $request->user()
        );

        return response()->json([
            'message' => 'Institusi berhasil ditambahkan.',
            'data'    => new InstitutionResource($institution),
        ], 201);
    }

    /**
     * GET /api/v1/admin/institutions/{id}
     */
    public function show(string $id): JsonResponse
    {
        $institution = $this->service->findOrFail($id);
        $this->authorize('view', $institution);

        return response()->json([
            'data' => new InstitutionResource($institution),
        ]);
    }

    /**
     * PUT /api/v1/admin/institutions/{id}
     */
    public function update(UpdateInstitutionRequest $request, string $id): JsonResponse
    {
        $institution = $this->service->findOrFail($id);
        $this->authorize('update', $institution);

        $updated = $this->service->update(
            institution: $institution,
            validated:   $request->validated(),
            actor:       $request->user()
        );

        return response()->json([
            'message' => 'Institusi berhasil diperbarui.',
            'data'    => new InstitutionResource($updated),
        ]);
    }

    /**
     * GET /api/v1/admin/institutions/{id}/detail
     */
    public function showDetail(string $id): JsonResponse
    {
        $institution = $this->service->findOrFail($id);
        $this->authorize('view', $institution);

        return response()->json([
            'data' => new InstitutionResource($institution),
        ]);
    }

    /**
     * PUT /api/v1/admin/institutions/{id}/detail
     */
    public function updateDetail(UpdateInstitutionRequest $request, string $id): JsonResponse
    {
        $institution = $this->service->findOrFail($id);
        $this->authorize('update', $institution);

        $detailData = $request->validated()['detail'] ?? [];
        $updated = $this->service->updateDetail($institution, $detailData, $request->user());

        return response()->json([
            'message' => 'Detail institusi berhasil diperbarui.',
            'data'    => new InstitutionResource($updated),
        ]);
    }

    /**
     * DELETE /api/v1/admin/institutions/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $institution = $this->service->findOrFail($id);
        $this->authorize('delete', $institution);

        $this->service->delete($institution, $request->user());

        return response()->json([
            'message' => 'Institusi berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/institutions/{id}/restore
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $this->authorize('restore', Institution::class);

        $institution = $this->service->restore($id, $request->user());

        return response()->json([
            'message' => 'Institusi berhasil dipulihkan.',
            'data'    => new InstitutionResource($institution),
        ]);
    }
}
