<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Application;
use Modules\HR\Domain\Entities\PipelineStage;

class LinearRecruitmentPipelineStrategy implements RecruitmentPipelineStrategy
{
    public function canAdvance(Application $application, PipelineStage $fromStage, PipelineStage $toStage): bool
    {
        return $toStage->order > $fromStage->order;
    }

    public function onAdvance(Application $application, PipelineStage $fromStage, PipelineStage $toStage): void
    {
        // Linear strategy does not need extra side-effects for now.
    }

    public function getNextStage(PipelineStage $currentStage): ?PipelineStage
    {
        return PipelineStage::query()
            ->where('order', '>', $currentStage->order)
            ->orderBy('order')
            ->first();
    }

    public function getValidTransitions(PipelineStage $stage): array
    {
        return PipelineStage::query()
            ->where('order', '>', $stage->order)
            ->orderBy('order')
            ->pluck('id')
            ->toArray();
    }
}
