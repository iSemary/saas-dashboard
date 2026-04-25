<?php

namespace Modules\POS\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\POS\Domain\Entities\Product;

class ProductCreated
{
    use Dispatchable;

    public function __construct(public readonly Product $product) {}
}
