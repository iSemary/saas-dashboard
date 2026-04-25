<?php

namespace Modules\POS\Domain\Strategies\Stock;

use Modules\POS\Domain\Enums\StockModelType;
use Modules\POS\Domain\Contracts\ProductStockRepositoryInterface;

class DecrementStockStrategy implements StockOperationStrategyInterface
{
    public function __construct(
        private readonly ProductStockRepositoryInterface $stockRepository,
    ) {}

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
    ): bool {
        return $this->stockRepository->createStock([
            'product_id' => $productId,
            'branch_id'  => $branchId,
            'quantity'   => -abs($amount),
            'object_id'  => $objectId,
            'model'      => $model->value,
            'main_price' => $mainPrice,
            'total_price' => $totalPrice,
            'tag_id'     => $tagIds,
            'barcode'    => $barcode,
            'currency_id' => $currencyId,
            'created_by' => $createdBy,
        ]);
    }
}
