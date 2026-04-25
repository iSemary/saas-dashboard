<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyWebhook;

class EloquentSurveyWebhookRepository implements SurveyWebhookRepositoryInterface
{
    public function find(int $id): ?SurveyWebhook
    {
        return SurveyWebhook::find($id);
    }

    public function findOrFail(int $id): SurveyWebhook
    {
        return SurveyWebhook::findOrFail($id);
    }

    public function create(array $data): SurveyWebhook
    {
        return SurveyWebhook::create($data);
    }

    public function update(int $id, array $data): SurveyWebhook
    {
        $webhook = $this->findOrFail($id);
        $webhook->update($data);
        return $webhook->fresh();
    }

    public function delete(int $id): bool
    {
        $webhook = $this->find($id);
        return $webhook ? $webhook->delete() : false;
    }

    public function findBySurvey(int $surveyId): array
    {
        return SurveyWebhook::where('survey_id', $surveyId)->get()->toArray();
    }

    public function findActiveBySurvey(int $surveyId): array
    {
        return SurveyWebhook::where('survey_id', $surveyId)
            ->where('is_active', true)
            ->get()
            ->toArray();
    }

    public function findActiveByEvent(int $surveyId, string $event): array
    {
        return SurveyWebhook::where('survey_id', $surveyId)
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get()
            ->toArray();
    }
}
