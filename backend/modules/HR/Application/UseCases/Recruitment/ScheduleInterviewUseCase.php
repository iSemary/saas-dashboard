<?php

namespace Modules\HR\Application\UseCases\Recruitment;

use Carbon\Carbon;
use Modules\HR\Domain\Entities\Interview;
use Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\InterviewRepositoryInterface;

class ScheduleInterviewUseCase
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository,
        protected InterviewRepositoryInterface $interviewRepository,
    ) {}

    public function execute(int $applicationId, array $data): Interview
    {
        $application = $this->applicationRepository->findOrFail($applicationId);

        if ($application->status === 'hired' || $application->status === 'rejected') {
            throw new \RuntimeException('Cannot schedule interview for a closed application');
        }

        $interview = $this->interviewRepository->create([
            'application_id' => $applicationId,
            'candidate_id' => $application->candidate_id,
            'type' => $data['type'] ?? 'video',
            'scheduled_at' => Carbon::parse($data['scheduled_at']),
            'duration_minutes' => $data['duration_minutes'] ?? 30,
            'location' => $data['location'] ?? null,
            'meeting_link' => $data['meeting_link'] ?? null,
            'status' => 'scheduled',
            'created_by' => auth()->id(),
        ]);

        if (!empty($data['interviewer_ids'])) {
            $interview->interviewers()->attach($data['interviewer_ids']);
        }

        return $interview;
    }
}
