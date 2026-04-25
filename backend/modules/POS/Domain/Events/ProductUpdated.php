<?php

namespace Modules\POS\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\POS\Domain\Entities\Product;

class ProductUpdated
{
    use Dispatchable;

    public function __construct(public readonly Product $product) {}
}
