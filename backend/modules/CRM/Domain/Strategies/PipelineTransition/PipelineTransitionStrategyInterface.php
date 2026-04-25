<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\PipelineTransition;

use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;

interface PipelineTransitionStrategyInterface
{
    /**
     * Determine if a transition from one stage to another is allowed.
     */
    public function canTransition(
        Opportunity $opportunity,
        OpportunityStage $from,
        OpportunityStage $to
    ): bool;

    /**
     * Get the error message if transition is not allowed.
     */
    public function getTransitionError(): ?string;

    /**
     * Get valid target stages from a given stage.
     *
     * @return OpportunityStage[]
     */
    public function getValidTransitionsFrom(OpportunityStage $from): array;

    /**
     * Get the strategy name.
     */
    public function getName(): string;
}
