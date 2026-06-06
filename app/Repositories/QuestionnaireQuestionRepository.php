<?php

namespace App\Repositories;

use App\Models\QuestionnaireQuestion;
use Illuminate\Database\Eloquent\Collection;

class QuestionnaireQuestionRepository
{
    // ─── Read ─────────────────────────────────────────────────────────────────

    public function allByQuestionnaire(string $questionnaireId, bool $onlyActive = false): Collection
    {
        $query = QuestionnaireQuestion::with(['answerType'])
            ->where('questionnaire_id', $questionnaireId);

        if ($onlyActive) {
            $query->active();
        }

        return $query->orderBy('question_order')->get();
    }

    public function findById(string $id): ?QuestionnaireQuestion
    {
        return QuestionnaireQuestion::with(['answerType'])->find($id);
    }

    public function findByIdOrFail(string $id): QuestionnaireQuestion
    {
        return QuestionnaireQuestion::with(['answerType'])->findOrFail($id);
    }

    public function maxOrderByQuestionnaire(string $questionnaireId): int
    {
        return QuestionnaireQuestion::where('questionnaire_id', $questionnaireId)
            ->whereNull('deleted_at')
            ->max('question_order') ?? 0;
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function create(array $data): QuestionnaireQuestion
    {
        return QuestionnaireQuestion::create($data);
    }

    public function update(QuestionnaireQuestion $question, array $data): QuestionnaireQuestion
    {
        $question->update($data);
        return $question->fresh(['answerType']);
    }

    public function delete(QuestionnaireQuestion $question): void
    {
        $question->delete();
    }

    public function restore(string $id): QuestionnaireQuestion
    {
        $question = QuestionnaireQuestion::withTrashed()->findOrFail($id);
        $question->restore();
        return $question;
    }

    public function reorder(string $questionnaireId, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            QuestionnaireQuestion::where('id', $id)
                ->where('questionnaire_id', $questionnaireId)
                ->update(['question_order' => $index + 1]);
        }
    }
}
