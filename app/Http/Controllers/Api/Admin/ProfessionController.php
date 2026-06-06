<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProfessionRequest;
use App\Http\Requests\Admin\UpdateProfessionRequest;
use App\Http\Resources\ProfessionResource;
use App\Models\Profession;
use App\Services\ProfessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProfessionController extends Controller
{
    public function __construct(protected ProfessionService $professionService) {}

    /**
     * GET /api/v1/admin/professions
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Profession::class);

        $professions = $this->professionService->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only(['search', 'is_active', 'profession_category_id']),
            sortBy:  $request->input('sort_by', 'name'),
            sortDir: $request->input('sort_dir', 'asc')
        );

        return ProfessionResource::collection($professions);
    }

    /**
     * GET /api/v1/admin/professions/all
     * Dropdown — semua profesi aktif, opsional filter per kategori.
     */
    public function all(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Profession::class);

        return response()->json([
            'data' => ProfessionResource::collection(
                $this->professionService->allActive(
                    categoryId: $request->input('profession_category_id')
                )
            ),
        ]);
    }

    /**
     * POST /api/v1/admin/professions
     */
    public function store(StoreProfessionRequest $request): JsonResponse
    {
        $this->authorize('create', Profession::class);

        $profession = $this->professionService->create(
            validated: $request->validated(),
            actor: $request->user()
        );

        return response()->json([
            'message' => 'Profesi berhasil ditambahkan.',
            'data'    => new ProfessionResource($profession),
        ], 201);
    }

    /**
     * GET /api/v1/admin/professions/{id}
     */
    public function show(string $id): JsonResponse
    {
        $profession = $this->professionService->findOrFail($id);
        $this->authorize('view', $profession);

        return response()->json([
            'data' => new ProfessionResource($profession),
        ]);
    }

    /**
     * PUT /api/v1/admin/professions/{id}
     */
    public function update(UpdateProfessionRequest $request, string $id): JsonResponse
    {
        $profession = $this->professionService->findOrFail($id);
        $this->authorize('update', $profession);

        $updated = $this->professionService->update(
            profession: $profession,
            validated:  $request->validated(),
            actor:      $request->user()
        );

        return response()->json([
            'message' => 'Profesi berhasil diperbarui.',
            'data'    => new ProfessionResource($updated),
        ]);
    }

    /**
     * DELETE /api/v1/admin/professions/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $profession = $this->professionService->findOrFail($id);
        $this->authorize('delete', $profession);

        $this->professionService->delete($profession, $request->user());

        return response()->json([
            'message' => 'Profesi berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/professions/{id}/restore
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $this->authorize('restore', Profession::class);

        $profession = $this->professionService->restore($id, $request->user());

        return response()->json([
            'message' => 'Profesi berhasil dipulihkan.',
            'data'    => new ProfessionResource($profession),
        ]);
    }
}
