<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuestionnaireQuestion\ReorderQuestionnaireQuestionRequest;
use App\Http\Requests\Admin\QuestionnaireQuestion\StoreQuestionnaireQuestionRequest;
use App\Http\Requests\Admin\QuestionnaireQuestion\UpdateQuestionnaireQuestionRequest;
use App\Http\Resources\Admin\QuestionnaireQuestionCollection;
use App\Http\Resources\Admin\QuestionnaireQuestionResource;
use App\Services\QuestionnaireQuestionService;
use App\Services\QuestionnaireService;
use Illuminate\Http\JsonResponse;

class QuestionnaireQuestionController extends Controller
{
    public function __construct(
        protected QuestionnaireQuestionService $service,
        protected QuestionnaireService $questionnaireService,
    ) {}

    /** GET /admin/questionnaires/{questionnaire}/questions */
    public function index(string $questionnaireId): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findByIdOrFail($questionnaireId);
        $this->authorize('view', $questionnaire);

        $questions = $this->service->allByQuestionnaire($questionnaireId);

        return response()->json(new QuestionnaireQuestionCollection($questions));
    }

    /** POST /admin/questionnaires/{questionnaire}/questions */
    public function store(StoreQuestionnaireQuestionRequest $request, string $questionnaireId): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findByIdOrFail($questionnaireId);
        $this->authorize('update', $questionnaire);

        $question = $this->service->create($questionnaireId, $request->validated());

        return response()->json(new QuestionnaireQuestionResource($question), 201);
    }

    /** GET /admin/questionnaires/{questionnaire}/questions/{question} */
    public function show(string $questionnaireId, string $id): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findByIdOrFail($questionnaireId);
        $this->authorize('view', $questionnaire);

        $question = $this->service->findByIdOrFail($id);

        return response()->json(new QuestionnaireQuestionResource($question));
    }

    /** PUT /admin/questionnaires/{questionnaire}/questions/{question} */
    public function update(UpdateQuestionnaireQuestionRequest $request, string $questionnaireId, string $id): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findByIdOrFail($questionnaireId);
        $this->authorize('update', $questionnaire);

        $question = $this->service->findByIdOrFail($id);
        $updated = $this->service->update($question, $request->validated());

        return response()->json(new QuestionnaireQuestionResource($updated));
    }

    /** DELETE /admin/questionnaires/{questionnaire}/questions/{question} */
    public function destroy(string $questionnaireId, string $id): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findByIdOrFail($questionnaireId);
        $this->authorize('update', $questionnaire);

        $question = $this->service->findByIdOrFail($id);
        $this->service->delete($question);

        return response()->json(['message' => 'Pertanyaan berhasil dihapus.']);
    }

    /** PATCH /admin/questionnaires/{questionnaire}/questions/{question}/restore */
    public function restore(string $questionnaireId, string $id): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findByIdOrFail($questionnaireId);
        $this->authorize('update', $questionnaire);

        $restored = $this->service->restore($id);

        return response()->json(new QuestionnaireQuestionResource($restored));
    }

    /** PATCH /admin/questionnaires/{questionnaire}/questions/reorder */
    public function reorder(ReorderQuestionnaireQuestionRequest $request, string $questionnaireId): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findByIdOrFail($questionnaireId);
        $this->authorize('update', $questionnaire);

        $this->service->reorder($questionnaireId, $request->validated()['ordered_ids']);

        $questions = $this->service->allByQuestionnaire($questionnaireId);

        return response()->json(new QuestionnaireQuestionCollection($questions));
    }
}
