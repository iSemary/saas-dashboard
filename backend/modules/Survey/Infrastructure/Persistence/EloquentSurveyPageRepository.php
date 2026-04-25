<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyPage;

class EloquentSurveyPageRepository implements SurveyPageRepositoryInterface
{
    public function find(int $id): ?SurveyPage
    {
        return SurveyPage::find($id);
    }

    public function findOrFail(int $id): SurveyPage
    {
        return SurveyPage::findOrFail($id);
    }

    public function create(array $data): SurveyPage
    {
        return SurveyPage::create($data);
    }

    public function update(int $id, array $data): SurveyPage
    {
        $page = $this->findOrFail($id);
        $page->update($data);
        return $page->fresh();
    }

    public function delete(int $id): bool
    {
        $page = $this->find($id);
        return $page ? $page->delete() : false;
    }

    public function findBySurvey(int $surveyId): array
    {
        return SurveyPage::where('survey_id', $surveyId)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function reorder(int $surveyId, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            SurveyPage::where('id', $id)
                ->where('survey_id', $surveyId)
                ->update(['order' => $index + 1]);
        }
    }

    public function getNextPageOrder(int $surveyId): int
    {
        $maxOrder = SurveyPage::where('survey_id', $surveyId)->max('order') ?? 0;
        return $maxOrder + 1;
    }
}
