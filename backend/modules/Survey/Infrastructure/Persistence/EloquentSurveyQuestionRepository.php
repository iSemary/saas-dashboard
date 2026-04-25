<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyQuestion;

class EloquentSurveyQuestionRepository implements SurveyQuestionRepositoryInterface
{
    public function find(int $id): ?SurveyQuestion
    {
        return SurveyQuestion::find($id);
    }

    public function findOrFail(int $id): SurveyQuestion
    {
        return SurveyQuestion::findOrFail($id);
    }

    public function create(array $data): SurveyQuestion
    {
        return SurveyQuestion::create($data);
    }

    public function update(int $id, array $data): SurveyQuestion
    {
        $question = $this->findOrFail($id);
        $question->update($data);
        return $question->fresh();
    }

    public function delete(int $id): bool
    {
        $question = $this->find($id);
        return $question ? $question->delete() : false;
    }

    public function findBySurvey(int $surveyId): array
    {
        return SurveyQuestion::where('survey_id', $surveyId)
            ->with('options')
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function findByPage(int $pageId): array
    {
        return SurveyQuestion::where('page_id', $pageId)
            ->with('options')
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function reorder(int $surveyId, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            SurveyQuestion::where('id', $id)
                ->where('survey_id', $surveyId)
                ->update(['order' => $index + 1]);
        }
    }

    public function getNextQuestionOrder(int $pageId): int
    {
        $maxOrder = SurveyQuestion::where('page_id', $pageId)->max('order') ?? 0;
        return $maxOrder + 1;
    }

    public function findNextQuestion(SurveyQuestion $current): ?SurveyQuestion
    {
        return SurveyQuestion::where('survey_id', $current->survey_id)
            ->where('order', '>', $current->order)
            ->orderBy('order')
            ->first();
    }

    public function findFirstOfPage(int $pageId): ?SurveyQuestion
    {
        return SurveyQuestion::where('page_id', $pageId)
            ->orderBy('order')
            ->first();
    }

    public function getScorableQuestions(int $surveyId): array
    {
        return SurveyQuestion::where('survey_id', $surveyId)
            ->whereIn('type', ['multiple_choice', 'checkbox', 'rating', 'nps', 'yes_no', 'likert_scale'])
            ->with('options')
            ->get()
            ->toArray();
    }
}
