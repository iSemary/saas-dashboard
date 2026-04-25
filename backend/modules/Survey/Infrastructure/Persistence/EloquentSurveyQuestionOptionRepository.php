<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyQuestionOption;

class EloquentSurveyQuestionOptionRepository implements SurveyQuestionOptionRepositoryInterface
{
    public function find(int $id): ?SurveyQuestionOption
    {
        return SurveyQuestionOption::find($id);
    }

    public function findOrFail(int $id): SurveyQuestionOption
    {
        return SurveyQuestionOption::findOrFail($id);
    }

    public function create(array $data): SurveyQuestionOption
    {
        return SurveyQuestionOption::create($data);
    }

    public function update(int $id, array $data): SurveyQuestionOption
    {
        $option = $this->findOrFail($id);
        $option->update($data);
        return $option->fresh();
    }

    public function delete(int $id): bool
    {
        $option = $this->find($id);
        return $option ? $option->delete() : false;
    }

    public function findByQuestion(int $questionId): array
    {
        return SurveyQuestionOption::where('question_id', $questionId)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function reorder(int $questionId, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            SurveyQuestionOption::where('id', $id)
                ->where('question_id', $questionId)
                ->update(['order' => $index + 1]);
        }
    }
}
