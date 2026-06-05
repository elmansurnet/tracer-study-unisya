<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacultyRequest;
use App\Http\Requests\Admin\UpdateFacultyRequest;
use App\Http\Resources\FacultyResource;
use App\Models\Faculty;
use App\Services\FacultyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FacultyController extends Controller
{
    public function __construct(protected FacultyService $facultyService) {}

    /**
     * GET /api/v1/admin/faculties
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Faculty::class);

        $faculties = $this->facultyService->paginate(
            perPage: (int) $request->input('per_page', 15),
            filters: $request->only(['search', 'is_active']),
            sortBy:  $request->input('sort_by', 'name'),
            sortDir: $request->input('sort_dir', 'asc')
        );

        return FacultyResource::collection($faculties);
    }

    /**
     * GET /api/v1/admin/faculties/all
     * Dropdown — semua fakultas aktif tanpa paginasi.
     */
    public function all(): JsonResponse
    {
        $this->authorize('viewAny', Faculty::class);

        return response()->json([
            'data' => FacultyResource::collection($this->facultyService->allActive()),
        ]);
    }

    /**
     * POST /api/v1/admin/faculties
     */
    public function store(StoreFacultyRequest $request): JsonResponse
    {
        $this->authorize('create', Faculty::class);

        $faculty = $this->facultyService->create(
            validated: $request->validated(),
            actor: $request->user()
        );

        return response()->json([
            'message' => 'Fakultas berhasil ditambahkan.',
            'data'    => new FacultyResource($faculty),
        ], 201);
    }

    /**
     * GET /api/v1/admin/faculties/{id}
     */
    public function show(string $id): JsonResponse
    {
        $faculty = $this->facultyService->findOrFail($id);
        $this->authorize('view', $faculty);

        return response()->json([
            'data' => new FacultyResource($faculty),
        ]);
    }

    /**
     * PUT /api/v1/admin/faculties/{id}
     */
    public function update(UpdateFacultyRequest $request, string $id): JsonResponse
    {
        $faculty = $this->facultyService->findOrFail($id);
        $this->authorize('update', $faculty);

        $updated = $this->facultyService->update(
            faculty:   $faculty,
            validated: $request->validated(),
            actor:     $request->user()
        );

        return response()->json([
            'message' => 'Data fakultas berhasil diperbarui.',
            'data'    => new FacultyResource($updated),
        ]);
    }

    /**
     * DELETE /api/v1/admin/faculties/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $faculty = $this->facultyService->findOrFail($id);
        $this->authorize('delete', $faculty);

        $this->facultyService->delete($faculty, $request->user());

        return response()->json([
            'message' => 'Fakultas berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/v1/admin/faculties/{id}/restore
     */
    public function restore(Request $request, string $id): JsonResponse
    {
        $this->authorize('restore', Faculty::class);

        $faculty = $this->facultyService->restore($id, $request->user());

        return response()->json([
            'message' => 'Fakultas berhasil dipulihkan.',
            'data'    => new FacultyResource($faculty),
        ]);
    }
}
