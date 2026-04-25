<?php

namespace Modules\HR\Application\UseCases\Recruitment;

use Modules\HR\Domain\Entities\Offer;
use Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\OfferRepositoryInterface;

class AcceptOfferUseCase
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepository,
        protected OfferRepositoryInterface $offerRepository,
    ) {}

    public function execute(int $offerId): Offer
    {
        $offer = $this->offerRepository->findOrFail($offerId);

        if ($offer->status !== 'sent') {
            throw new \RuntimeException('Only sent offers can be accepted');
        }

        if ($offer->isExpired()) {
            throw new \RuntimeException('Offer has expired');
        }

        $offer->accept();

        // Mark application as hired
        $application = $this->applicationRepository->findOrFail($offer->application_id);
        $application->markAsHired();

        return $this->offerRepository->findOrFail($offerId);
    }
}
