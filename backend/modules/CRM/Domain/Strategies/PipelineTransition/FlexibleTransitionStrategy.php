<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\PipelineTransition;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;

/**
 * Flexible pipeline transition strategy - allows skipping stages.
 */
class FlexibleTransitionStrategy implements PipelineTransitionStrategyInterface
{
    private ?string $errorMessage = null;

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

        // Can always move to closed lost from any stage
        if ($to === OpportunityStage::CLOSED_LOST) {
            return true;
        }

        // Can move to any open stage except backwards to prospecting after qualification
        if ($to === OpportunityStage::PROSPECTING && $from !== OpportunityStage::PROSPECTING) {
            $this->errorMessage = 'Cannot move back to Prospecting after progressing';

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

        $openStages = OpportunityStage::openStages();

        // Filter out current stage and prospecting (if not already there)
        return array_filter($openStages, function (OpportunityStage $stage) use ($from) {
            if ($stage === $from) {
                return false;
            }

            // Can't go back to prospecting
            if ($stage === OpportunityStage::PROSPECTING && $from !== OpportunityStage::PROSPECTING) {
                return false;
            }

            return true;
        });
    }

    public function getName(): string
    {
        return 'Flexible (Allow Skipping)';
    }
}
