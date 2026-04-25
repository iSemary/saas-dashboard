<?php

namespace Modules\HR\Application\UseCases\Recruitment;

use Modules\HR\Domain\Entities\Offer;
use Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\OfferRepositoryInterface;

class MakeOfferUseCase
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository,
        protected OfferRepositoryInterface $offerRepository,
    ) {}

    public function execute(int $applicationId, array $data): Offer
    {
        $application = $this->applicationRepository->findOrFail($applicationId);

        if ($application->status === 'hired' || $application->status === 'rejected') {
            throw new \RuntimeException('Cannot make offer for a closed application');
        }

        return $this->offerRepository->create([
            'application_id' => $application->id,
            'candidate_id' => $application->candidate_id,
            'job_opening_id' => $application->job_opening_id,
            'salary' => $data['salary'],
            'currency' => $data['currency'] ?? 'USD',
            'bonus' => $data['bonus'] ?? 0,
            'benefits' => $data['benefits'] ?? null,
            'start_date' => $data['start_date'],
            'expiry_date' => $data['expiry_date'],
            'status' => 'draft',
            'terms' => $data['terms'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }
}
