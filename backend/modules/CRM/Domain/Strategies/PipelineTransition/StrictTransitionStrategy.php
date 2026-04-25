<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\PipelineTransition;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;

/**
 * Strict pipeline transition strategy - enforces sequential progression.
 * Opportunities must move through stages in order without skipping.
 */
class StrictTransitionStrategy implements PipelineTransitionStrategyInterface
{
    private ?string $errorMessage = null;

    /**
     * Ordered list of stages representing the pipeline flow.
     */
    private const STAGE_ORDER = [
        OpportunityStage::PROSPECTING,
        OpportunityStage::QUALIFICATION,
        OpportunityStage::NEEDS_ANALYSIS,
        OpportunityStage::VALUE_PROPOSITION,
        OpportunityStage::IDDECISION_MAKERS,
        OpportunityStage::PERCEPTION_ANALYSIS,
        OpportunityStage::PROPOSAL_PRICE_QUOTE,
        OpportunityStage::NEGOTIATION_REVIEW,
        OpportunityStage::CLOSED_WON,
        OpportunityStage::CLOSED_LOST,
    ];

    public function canTransition(
        Opportunity $opportunity,
        OpportunityStage $from,
        OpportunityStage $to
    ): bool {
        $this->errorMessage = null;

        // Terminal stages cannot transition out
        if ($from === OpportunityStage::CLOSED_WON || $from === OpportunityStage::CLOSED_LOST) {
            $this->errorMessage = "Cannot transition from terminal stage: {$from->label()}";

            return false;
        }

        // Can always move to closed lost from any open stage
        if ($to === OpportunityStage::CLOSED_LOST) {
            return true;
        }

        // Get stage indices
        $fromIndex = $this->getStageIndex($from);
        $toIndex = $this->getStageIndex($to);

        if ($fromIndex === null || $toIndex === null) {
            $this->errorMessage = 'Invalid stage for transition';

            return false;
        }

        // Strict: can only move forward by one step
        if ($toIndex !== $fromIndex + 1) {
            $this->errorMessage = "Strict mode: Can only move to the next stage ({$from->label()} → " .
                self::STAGE_ORDER[$fromIndex + 1]?->label() . ')'; // phpcs:ignore

            return false;
        }

        return true;
    }

    public function getTransitionError(): ?string
    {
        return $this->errorMessage;
    }

    public function getValidTransitionsFrom(OpportunityStage $from): array
    {
        if ($from === OpportunityStage::CLOSED_WON || $from === OpportunityStage::CLOSED_LOST) {
            return [];
        }

        $fromIndex = $this->getStageIndex($from);
        if ($fromIndex === null) {
            return [];
        }

        // In strict mode, can only go to next stage or closed lost
        $valid = [];

        // Next sequential stage
        if (isset(self::STAGE_ORDER[$fromIndex + 1])) {
            $valid[] = self::STAGE_ORDER[$fromIndex + 1];
        }

        // Can always go to closed lost
        $valid[] = OpportunityStage::CLOSED_LOST;

        return $valid;
    }

    public function getName(): string
    {
        return 'Strict Sequential';
    }

    private function getStageIndex(OpportunityStage $stage): ?int
    {
        foreach (self::STAGE_ORDER as $index => $orderedStage) {
            if ($orderedStage === $stage) {
                return $index;
            }
        }

        return null;
    }
}
