<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyWebhook;

interface SurveyWebhookRepositoryInterface
{
    public function find(int $id): ?SurveyWebhook;
    public function findOrFail(int $id): SurveyWebhook;
    public function create(array $data): SurveyWebhook;
    public function update(int $id, array $data): SurveyWebhook;
    public function delete(int $id): bool;
    public function findBySurvey(int $surveyId): array;
    public function findActiveBySurvey(int $surveyId): array;
    public function findActiveByEvent(int $surveyId, string $event): array;
}
