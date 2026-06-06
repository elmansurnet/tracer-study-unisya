<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProfessionCategoryRequest;
use App\Http\Requests\Admin\UpdateProfessionCategoryRequest;
use App\Http\Resources\ProfessionCategoryResource;
use App\Models\ProfessionCategory;
use App\Services\ProfessionCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProfessionCategoryController extends Controller
{
    public function __construct(protected ProfessionCategoryService $service) {}

    /**
     * GET /api/v1/admin/profession-categories
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ProfessionCategory::class);

        $categories = $this->service->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only(['search', 'is_active']),
            sortBy:  $request->input('sort_by', 'name'),
            sortDir: $request->input('sort_dir', 'asc')
        );

        return ProfessionCategoryResource::collection($categories);
    }

    /**
     * GET /api/v1/admin/profession-categories/all
     */
    public function all(): JsonResponse
    {
        $this->authorize('viewAny', ProfessionCategory::class);

        return response()->json([
            'data' => ProfessionCategoryResource::collection($this->service->allActive()),
        ]);
    }

    /**
     * POST /api/v1/admin/profession-categories
     */
    public function store(StoreProfessionCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', ProfessionCategory::class);

        $category = $this->service->create(
            validated: $request->validated(),
            actor: $request->user()
        );

        return response()->json([
            'message' => 'Kategori profesi berhasil ditambahkan.',
            'data'    => new ProfessionCategoryResource($category),
        ], 201);
    }

    /**
     * GET /api/v1/admin/profession-categories/{id}
     */
    public function show(string $id): JsonResponse
    {
        $category = $this->service->findOrFail($id);
        $this->authorize('view', $category);

        return response()->json([
            'data' => new ProfessionCategoryResource($category),
        ]);
    }

    /**
     * PUT /api/v1/admin/profession-categories/{id}
     */
    public function update(UpdateProfessionCategoryRequest $request, string $id): JsonResponse
    {
        $category = $this->service->findOrFail($id);
        $this->authorize('update', $category);

        $updated = $this->service->update(
            category:  $category,
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Kategori profesi berhasil diperbarui.',
            'data'    => new ProfessionCategoryResource($updated),
        ]);
    }

    /**
     * DELETE /api/v1/admin/profession-categories/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $category = $this->service->findOrFail($id);
        $this->authorize('delete', $category);

        $this->service->delete($category, $request->user());

        return response()->json([
            'message' => 'Kategori profesi berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/profession-categories/{id}/restore
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $this->authorize('restore', ProfessionCategory::class);

        $category = $this->service->restore($id, $request->user());

        return response()->json([
            'message' => 'Kategori profesi berhasil dipulihkan.',
            'data'    => new ProfessionCategoryResource($category),
        ]);
    }
}
