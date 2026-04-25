<?php

namespace Modules\POS\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\POS\Domain\Entities\OfferPrice;

class OfferPriceCreated
{
    use Dispatchable;

    public function __construct(public readonly OfferPrice $offerPrice) {}
}
