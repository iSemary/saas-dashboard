<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\LeadQualification;

use Modules\CRM\Domain\Entities\Lead;
use Modules\CRM\Domain\ValueObjects\LeadStatus;

/**
 * Basic qualification strategy that qualifies leads based on status and engagement.
 */
class BasicQualificationStrategy implements LeadQualificationStrategyInterface
{
    public function canQualify(Lead $lead): bool
    {
        // Can only qualify leads that are not already qualified or converted
        $disqualifiedStatuses = [
            LeadStatus::QUALIFIED->value,
            LeadStatus::CONVERTED->value,
            LeadStatus::UNQUALIFIED->value,
        ];

        return !in_array($lead->status, $disqualifiedStatuses, true);
    }

    public function qualify(Lead $lead): LeadQualificationResult
    {
        if (!$this->canQualify($lead)) {
            return new LeadQualificationResult(
                isQualified: false,
                reason: 'Lead cannot be qualified in current status: ' . $lead->status
            );
        }

        $criteria = $this->evaluateCriteria($lead);
        $passedCount = count(array_filter($criteria, fn ($c) => $c['passed']));
        $totalCount = count($criteria);

        // Need at least 3 out of 5 criteria to pass
        $isQualified = $passedCount >= 3;
        $score = (int) round(($passedCount / $totalCount) * 100);

        return new LeadQualificationResult(
            isQualified: $isQualified,
            score: $score,
            reason: $isQualified
                ? "Lead qualified with {$passedCount}/{$totalCount} criteria passed"
                : "Lead not qualified ({$passedCount}/{$totalCount} criteria passed)",
            criteria: $criteria
        );
    }

    public function getName(): string
    {
        return 'Basic Qualification';
    }

    public function getDescription(): string
    {
        return 'Qualifies leads based on basic criteria like contact info completeness and engagement.';
    }

    private function evaluateCriteria(Lead $lead): array
    {
        return [
            [
                'name' => 'Has valid email',
                'passed' => !empty($lead->email) && filter_var($lead->email, FILTER_VALIDATE_EMAIL),
                'weight' => 1,
            ],
            [
                'name' => 'Has phone number',
                'passed' => !empty($lead->phone),
                'weight' => 1,
            ],
            [
                'name' => 'Has company name',
                'passed' => !empty($lead->company),
                'weight' => 1,
            ],
            [
                'name' => 'Has expected revenue',
                'passed' => !empty($lead->expected_revenue) && $lead->expected_revenue > 0,
                'weight' => 1,
            ],
            [
                'name' => 'Has close date',
                'passed' => !empty($lead->expected_close_date),
                'weight' => 1,
            ],
        ];
    }
}
