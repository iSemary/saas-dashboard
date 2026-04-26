<?php

namespace Modules\HR\Application\UseCases\Recruitment;

use Carbon\Carbon;
use Modules\HR\Domain\Entities\Application;
use Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\CandidateRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\JobOpeningRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\PipelineStageRepositoryInterface;

class ApplyToJobUseCase
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository,
        protected CandidateRepositoryInterface $candidateRepository,
        protected JobOpeningRepositoryInterface $jobOpeningRepository,
        protected PipelineStageRepositoryInterface $pipelineStageRepository,
    ) {}

    public function execute(array $data): Application
    {
        // Check if job opening exists and is published
        $jobOpening = $this->jobOpeningRepository->findOrFail($data['job_opening_id']);
        
        if (!$jobOpening->isPublished()) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        // Create or find candidate
        $candidateData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'current_title' => $data['current_title'] ?? null,
            'current_company' => $data['current_company'] ?? null,
            'resume_path' => $data['resume_path'] ?? null,
            'source' => $data['source'] ?? 'direct',
            'created_by' => auth()->id(),
        ];

        $candidate = $this->candidateRepository->findByEmail($data['email']);
        if (!$candidate) {
            $candidate = $this->candidateRepository->create($candidateData);
        }

        // Check for existing application
        $existingApplication = $this->applicationRepository
            ->findByCandidateAndJobOpening($candidate->id, $jobOpening->id);
        
        if ($existingApplication) {
            throw new \RuntimeException(translate('message.operation_failed'));
        }

        // Get default pipeline stage
        $defaultStage = $this->pipelineStageRepository->getDefault();
        
        // Create application
        $applicationData = [
            'job_opening_id' => $jobOpening->id,
            'candidate_id' => $candidate->id,
            'pipeline_stage_id' => $defaultStage?->id,
            'status' => 'new',
            'applied_at' => Carbon::now(),
            'cover_letter' => $data['cover_letter'] ?? null,
            'answers' => $data['answers'] ?? null,
            'source' => $data['source'] ?? 'direct',
            'salary_expectation' => $data['salary_expectation'] ?? null,
            'available_from' => $data['available_from'] ?? null,
            'created_by' => auth()->id(),
        ];

        return $this->applicationRepository->create($applicationData);
    }
}
