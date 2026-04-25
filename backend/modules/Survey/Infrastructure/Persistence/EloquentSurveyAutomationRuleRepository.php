<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyAutomationRule;

class EloquentSurveyAutomationRuleRepository implements SurveyAutomationRuleRepositoryInterface
{
    public function find(int $id): ?SurveyAutomationRule
    {
        return SurveyAutomationRule::find($id);
    }

    public function findOrFail(int $id): SurveyAutomationRule
    {
        return SurveyAutomationRule::findOrFail($id);
    }

    public function create(array $data): SurveyAutomationRule
    {
        return SurveyAutomationRule::create($data);
    }

    public function update(int $id, array $data): SurveyAutomationRule
    {
        $rule = $this->findOrFail($id);
        $rule->update($data);
        return $rule->fresh();
    }

    public function delete(int $id): bool
    {
        $rule = $this->find($id);
        return $rule ? $rule->delete() : false;
    }

    public function findBySurvey(int $surveyId): array
    {
        return SurveyAutomationRule::where('survey_id', $surveyId)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function findActiveBySurvey(int $surveyId): array
    {
        return SurveyAutomationRule::where('survey_id', $surveyId)
            ->where('is_active', true)
            ->get()
            ->toArray();
    }

    public function findActiveByTrigger(int $surveyId, string $triggerType): array
    {
        return SurveyAutomationRule::where('survey_id', $surveyId)
            ->where('is_active', true)
            ->where('trigger_type', $triggerType)
            ->get()
            ->toArray();
    }
}
