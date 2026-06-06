<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Questionnaire\StoreQuestionnaireRequest;
use App\Http\Requests\Admin\Questionnaire\UpdateQuestionnaireRequest;
use App\Http\Resources\Admin\QuestionnaireCollection;
use App\Http\Resources\Admin\QuestionnaireResource;
use App\Services\QuestionnaireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function __construct(
        protected QuestionnaireService $service
    ) {}

    /** GET /admin/questionnaires */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Questionnaire::class);

        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only([
            'search',
            'questionnaire_category_id',
            'respondent_type',
            'scope',
            'is_active',
        ]);

        $paginated = $this->service->paginate($filters, $perPage);

        return response()->json(new QuestionnaireCollection($paginated));
    }

    /** GET /admin/questionnaires/all */
    public function all(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Questionnaire::class);

        $questionnaires = $this->service->all(onlyActive: true);

        return response()->json([
            'data' => QuestionnaireResource::collection($questionnaires),
        ]);
    }

    /** POST /admin/questionnaires */
    public function store(StoreQuestionnaireRequest $request): JsonResponse
    {
        $questionnaire = $this->service->create($request->validated());

        return response()->json(
            new QuestionnaireResource($questionnaire->load(['category', 'faculty', 'studyProgram'])),
            201
        );
    }

    /** GET /admin/questionnaires/{questionnaire} */
    public function show(string $id): JsonResponse
    {
        $questionnaire = $this->service->findByIdOrFail($id, withQuestions: true);
        $this->authorize('view', $questionnaire);

        return response()->json(new QuestionnaireResource($questionnaire));
    }

    /** PUT /admin/questionnaires/{questionnaire} */
    public function update(UpdateQuestionnaireRequest $request, string $id): JsonResponse
    {
        $questionnaire = $this->service->findByIdOrFail($id);
        $this->authorize('update', $questionnaire);

        $updated = $this->service->update($questionnaire, $request->validated());

        return response()->json(new QuestionnaireResource($updated));
    }

    /** DELETE /admin/questionnaires/{questionnaire} */
    public function destroy(string $id): JsonResponse
    {
        $questionnaire = $this->service->findByIdOrFail($id);
        $this->authorize('delete', $questionnaire);

        $this->service->delete($questionnaire);

        return response()->json(['message' => 'Kuesioner berhasil dihapus.']);
    }

    /** PATCH /admin/questionnaires/{questionnaire}/restore */
    public function restore(string $id): JsonResponse
    {
        $restored = $this->service->restore($id);

        return response()->json(new QuestionnaireResource($restored));
    }
}
