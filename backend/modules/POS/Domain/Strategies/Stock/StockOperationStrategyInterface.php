<?php

namespace Modules\POS\Domain\Strategies\Stock;

use Modules\POS\Domain\Enums\StockModelType;

interface StockOperationStrategyInterface
{
    public function execute(
        int $productId,
        ?int $branchId,
        float $amount,
        StockModelType $model,
        int $objectId,
        ?float $mainPrice = null,
        ?float $totalPrice = null,
        ?array $tagIds = null,
        ?string $barcode = null,
        ?int $currencyId = null,
        int $createdBy = 0,
    ): bool;
}
