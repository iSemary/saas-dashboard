<?php

namespace Modules\HR\Application\UseCases\Recruitment;

use Modules\HR\Domain\Entities\Application;
use Modules\HR\Domain\Entities\PipelineStage;
use Modules\HR\Domain\Strategies\RecruitmentPipelineStrategy;
use Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\PipelineStageRepositoryInterface;

class AdvanceCandidateUseCase
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository,
        protected PipelineStageRepositoryInterface $pipelineStageRepository,
        protected RecruitmentPipelineStrategy $pipelineStrategy,
    ) {}

    public function execute(int $applicationId, int $toStageId): Application
    {
        $application = $this->applicationRepository->findOrFail($applicationId);
        $toStage = $this->pipelineStageRepository->findOrFail($toStageId);

        $fromStage = $application->pipelineStage;

        if ($fromStage && !$this->pipelineStrategy->canAdvance($application, $fromStage, $toStage)) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        $application->advanceToStage($toStage);

        if ($fromStage) {
            $this->pipelineStrategy->onAdvance($application, $fromStage, $toStage);
        }

        return $application->fresh();
    }
}
