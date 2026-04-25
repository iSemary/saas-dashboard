<?php

namespace Modules\POS\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\POS\Application\DTOs\CreateProductData;
use Modules\POS\Application\DTOs\UpdateProductData;
use Modules\POS\Domain\Contracts\ProductRepositoryInterface;
use Modules\POS\Domain\Contracts\BarcodeRepositoryInterface;
use Modules\POS\Domain\Contracts\ProductStockRepositoryInterface;
use Modules\POS\Domain\Entities\Product;
use Modules\POS\Domain\Enums\StockDirection;
use Modules\POS\Domain\Enums\StockModelType;
use Modules\POS\Domain\Events\ProductCreated;
use Modules\POS\Domain\Events\ProductUpdated;
use Modules\POS\Domain\Events\StockChanged;
use Modules\POS\Domain\Strategies\Stock\StockOperationStrategyInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository,
        private readonly BarcodeRepositoryInterface $barcodeRepository,
        private readonly ProductStockRepositoryInterface $stockRepository,
        private readonly StockOperationStrategyInterface $decrementStock,
        private readonly StockOperationStrategyInterface $incrementStock,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Product
    {
        return $this->repository->findOrFail($id);
    }

    public function findByBarcode(string $barcode): ?Product
    {
        return $this->repository->findByBarcode($barcode);
    }

    public function create(CreateProductData $data, int $userId): Product
    {
        return DB::transaction(function () use ($data, $userId) {
            $productData = array_merge($data->toArray(), ['created_by' => $userId]);
            $product = $this->repository->create($productData);

            if ($data->barcode_number) {
                $this->barcodeRepository->create([
                    'barcode_number' => $data->barcode_number,
                    'product_id'     => $product->id,
                    'category_id'    => $data->category_id,
                    'created_by'     => $userId,
                ]);
            }

            if ($data->amount > 0) {
                $this->stockRepository->createStock([
                    'product_id' => $product->id,
                    'quantity'   => $data->amount,
                    'model'      => StockModelType::Purchase->value,
                    'object_id'  => $product->id,
                    'created_by' => $userId,
                ]);
            }

            ProductCreated::dispatch($product);

            return $product;
        });
    }

    public function update(int $id, UpdateProductData $data, int $userId): Product
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $product = $this->repository->update($id, $data->toArray());

            if ($data->barcode_number) {
                $existing = $this->barcodeRepository->findByNumber($data->barcode_number);
                if (!$existing) {
                    $this->barcodeRepository->create([
                        'barcode_number' => $data->barcode_number,
                        'product_id'     => $product->id,
                        'category_id'    => $data->category_id,
                        'created_by'     => $userId,
                    ]);
                }
            }

            ProductUpdated::dispatch($product);

            return $product;
        });
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    public function changeStock(int $productId, float $amount, StockDirection $direction, ?int $branchId, int $userId): bool
    {
        $model = $direction === StockDirection::Increment
            ? StockModelType::Purchase
            : StockModelType::Order;

        $strategy = $direction === StockDirection::Increment
            ? $this->incrementStock
            : $this->decrementStock;

        $result = $strategy->execute(
            productId:  $productId,
            branchId:   $branchId,
            amount:     $amount,
            model:      $model,
            objectId:   $productId,
            createdBy:  $userId,
        );

        if ($result) {
            StockChanged::dispatch($productId, $amount, $direction, $model, $productId, $branchId);
        }

        return $result;
    }

    public function getAvailableStock(int $productId, ?int $branchId = null): int
    {
        return $this->stockRepository->getAvailableStock($productId, $branchId);
    }
}
