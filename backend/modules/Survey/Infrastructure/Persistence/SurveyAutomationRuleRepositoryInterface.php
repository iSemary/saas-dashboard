<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyAutomationRule;

interface SurveyAutomationRuleRepositoryInterface
{
    public function find(int $id): ?SurveyAutomationRule;
    public function findOrFail(int $id): SurveyAutomationRule;
    public function create(array $data): SurveyAutomationRule;
    public function update(int $id, array $data): SurveyAutomationRule;
    public function delete(int $id): bool;
    public function findBySurvey(int $surveyId): array;
    public function findActiveBySurvey(int $surveyId): array;
    public function findActiveByTrigger(int $surveyId, string $triggerType): array;
}
