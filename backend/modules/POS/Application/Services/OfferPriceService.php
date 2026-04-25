<?php

namespace Modules\POS\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\POS\Domain\Contracts\OfferPriceRepositoryInterface;
use Modules\POS\Domain\Contracts\ProductRepositoryInterface;
use Modules\POS\Domain\Contracts\ProductStockRepositoryInterface;
use Modules\POS\Domain\Entities\OfferPrice;
use Modules\POS\Domain\Enums\StockDirection;
use Modules\POS\Domain\Enums\StockModelType;
use Modules\POS\Domain\Events\OfferPriceCreated;
use Modules\POS\Domain\Events\StockChanged;
use Modules\POS\Domain\Strategies\Stock\StockOperationStrategyInterface;

class OfferPriceService
{
    public function __construct(
        private readonly OfferPriceRepositoryInterface $repository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductStockRepositoryInterface $stockRepository,
        private readonly StockOperationStrategyInterface $decrementStock,
        private readonly StockOperationStrategyInterface $incrementStock,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): OfferPrice
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): OfferPrice
    {
        return DB::transaction(function () use ($data, $userId) {
            $product = $this->productRepository->findOrFail($data['product_id']);
            $offerPrice = (float) ($data['amount'] ?? 0);

            $data['original_price'] = $product->sale_price;
            $data['total_price']    = $offerPrice * (float) ($data['amount'] ?? 1);
            $data['created_by']     = $userId;

            $record = $this->repository->create($data);

            if ($record->shouldReduceStock()) {
                $this->decrementStock->execute(
                    productId: $product->id,
                    branchId:  $data['branch_id'] ?? null,
                    amount:    $data['amount'],
                    model:     StockModelType::OfferPrice,
                    objectId:  $record->id,
                    mainPrice: $record->original_price,
                    totalPrice: $record->total_price,
                    createdBy: $userId,
                );
                StockChanged::dispatch($product->id, $data['amount'], StockDirection::Decrement, StockModelType::OfferPrice, $record->id);
            }

            OfferPriceCreated::dispatch($record);

            return $record;
        });
    }

    public function update(int $id, array $data): OfferPrice
    {
        return DB::transaction(function () use ($id, $data) {
            $old = $this->repository->findOrFail($id);
            $record = $this->repository->update($id, $data);

            if ($old->shouldReduceStock()) {
                $this->stockRepository->updateExistingStock(
                    $id,
                    StockModelType::OfferPrice->value,
                    $data['amount'] ?? $old->amount,
                    $data['total_price'] ?? $old->total_price,
                );
            }

            return $record;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->repository->findOrFail($id);

            if ($record->shouldReduceStock()) {
                $this->incrementStock->execute(
                    productId: $record->product_id,
                    branchId:  $record->branch_id,
                    amount:    $record->amount,
                    model:     StockModelType::OfferPrice,
                    objectId:  $record->id,
                    mainPrice: $record->original_price,
                    totalPrice: $record->total_price,
                    createdBy: auth()->id() ?? 0,
                );
            }

            return $this->repository->delete($id);
        });
    }
}
