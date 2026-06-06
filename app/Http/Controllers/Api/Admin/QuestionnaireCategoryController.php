<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuestionnaireCategory\StoreQuestionnaireCategoryRequest;
use App\Http\Requests\Admin\QuestionnaireCategory\UpdateQuestionnaireCategoryRequest;
use App\Http\Resources\Admin\QuestionnaireCategoryCollection;
use App\Http\Resources\Admin\QuestionnaireCategoryResource;
use App\Services\QuestionnaireCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionnaireCategoryController extends Controller
{
    public function __construct(
        protected QuestionnaireCategoryService $service
    ) {}

    /** GET /admin/questionnaire-categories */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\QuestionnaireCategory::class);

        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'is_active']);

        $paginated = $this->service->paginate($filters, $perPage);

        return response()->json(new QuestionnaireCategoryCollection($paginated));
    }

    /** GET /admin/questionnaire-categories/all */
    public function all(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\QuestionnaireCategory::class);

        $categories = $this->service->all(onlyActive: true);

        return response()->json([
            'data' => QuestionnaireCategoryResource::collection($categories),
        ]);
    }

    /** POST /admin/questionnaire-categories */
    public function store(StoreQuestionnaireCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());

        return response()->json(new QuestionnaireCategoryResource($category), 201);
    }

    /** GET /admin/questionnaire-categories/{questionnaire_category} */
    public function show(string $id): JsonResponse
    {
        $category = $this->service->findByIdOrFail($id);
        $this->authorize('view', $category);

        return response()->json(new QuestionnaireCategoryResource($category));
    }

    /** PUT /admin/questionnaire-categories/{questionnaire_category} */
    public function update(UpdateQuestionnaireCategoryRequest $request, string $id): JsonResponse
    {
        $category = $this->service->findByIdOrFail($id);
        $this->authorize('update', $category);

        $updated = $this->service->update($category, $request->validated());

        return response()->json(new QuestionnaireCategoryResource($updated));
    }

    /** DELETE /admin/questionnaire-categories/{questionnaire_category} */
    public function destroy(string $id): JsonResponse
    {
        $category = $this->service->findByIdOrFail($id);
        $this->authorize('delete', $category);

        $this->service->delete($category);

        return response()->json(['message' => 'Kategori kuesioner berhasil dihapus.']);
    }

    /** PATCH /admin/questionnaire-categories/{questionnaire_category}/restore */
    public function restore(string $id): JsonResponse
    {
        $restored = $this->service->restore($id);

        return response()->json(new QuestionnaireCategoryResource($restored));
    }
}
