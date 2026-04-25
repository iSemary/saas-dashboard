<?php

namespace Modules\HR\Domain\Events;

use Modules\HR\Domain\Entities\Offer;

class OfferAccepted
{
    public function __construct(
        public Offer $offer,
    ) {}
}
