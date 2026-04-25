<?php

namespace Modules\POS\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\POS\Domain\Enums\StockModelType;
use Modules\POS\Domain\Enums\StockDirection;

class StockChanged
{
    use Dispatchable;

    public function __construct(
        public readonly int $productId,
        public readonly float $amount,
        public readonly StockDirection $direction,
        public readonly StockModelType $model,
        public readonly int $objectId,
        public readonly ?int $branchId = null,
    ) {}
}
