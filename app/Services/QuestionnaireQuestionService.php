<?php

namespace App\Services;

use App\Models\QuestionnaireQuestion;
use App\Repositories\QuestionnaireQuestionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class QuestionnaireQuestionService
{
    public function __construct(
        protected QuestionnaireQuestionRepository $repo,
        protected AuditService $auditService,
    ) {}

    // ─── Read ─────────────────────────────────────────────────────────────────

    public function allByQuestionnaire(string $questionnaireId, bool $onlyActive = false): Collection
    {
        return $this->repo->allByQuestionnaire($questionnaireId, $onlyActive);
    }

    public function findByIdOrFail(string $id): QuestionnaireQuestion
    {
        return $this->repo->findByIdOrFail($id);
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(string $questionnaireId, array $validated): QuestionnaireQuestion
    {
        $nextOrder = $this->repo->maxOrderByQuestionnaire($questionnaireId) + 1;

        $data = array_merge($validated, [
            'questionnaire_id' => $questionnaireId,
            'question_order'   => $nextOrder,
            'created_by'       => Auth::id(),
        ]);

        $question = $this->repo->create($data);

        $this->auditService->log(
            event: 'created',
            model: $question,
            newValues: $question->toArray()
        );

        return $question->load('answerType');
    }

    public function update(QuestionnaireQuestion $question, array $validated): QuestionnaireQuestion
    {
        $old = $question->toArray();
        $data = array_merge($validated, ['updated_by' => Auth::id()]);
        $question = $this->repo->update($question, $data);

        $this->auditService->log(
            event: 'updated',
            model: $question,
            oldValues: $old,
            newValues: $question->toArray()
        );

        return $question;
    }

    public function delete(QuestionnaireQuestion $question): void
    {
        $this->repo->update($question, ['deleted_by' => Auth::id()]);
        $this->auditService->log(event: 'deleted', model: $question);
        $this->repo->delete($question);
    }

    public function restore(string $id): QuestionnaireQuestion
    {
        $question = $this->repo->restore($id);
        $this->repo->update($question, ['deleted_by' => null, 'updated_by' => Auth::id()]);
        $this->auditService->log(event: 'restored', model: $question);
        return $question;
    }

    public function reorder(string $questionnaireId, array $orderedIds): void
    {
        $this->repo->reorder($questionnaireId, $orderedIds);
    }
}
