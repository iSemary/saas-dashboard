<?php

namespace Modules\HR\Domain\Strategies;

use Modules\HR\Domain\Entities\Application;
use Modules\HR\Domain\Entities\PipelineStage;

interface RecruitmentPipelineStrategy
{
    public function canAdvance(Application $application, PipelineStage $fromStage, PipelineStage $toStage): bool;
    public function onAdvance(Application $application, PipelineStage $fromStage, PipelineStage $toStage): void;
    public function getNextStage(PipelineStage $currentStage): ?PipelineStage;
    public function getValidTransitions(PipelineStage $stage): array;
}
