<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\LeadQualification;

use Modules\CRM\Domain\Entities\Lead;

interface LeadQualificationStrategyInterface
{
    /**
     * Determine if the lead can be qualified using this strategy.
     */
    public function canQualify(Lead $lead): bool;

    /**
     * Qualify the lead and return the result.
     */
    public function qualify(Lead $lead): LeadQualificationResult;

    /**
     * Get the strategy name for display.
     */
    public function getName(): string;

    /**
     * Get the strategy description.
     */
    public function getDescription(): string;
}

final readonly class LeadQualificationResult
{
    public function __construct(
        public bool $isQualified,
        public ?string $reason = null,
        public ?int $score = null,
        public array $criteria = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'is_qualified' => $this->isQualified,
            'reason' => $this->reason,
            'score' => $this->score,
            'criteria' => $this->criteria,
        ];
    }
}
