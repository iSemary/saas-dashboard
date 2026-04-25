<?php

namespace Modules\POS\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\POS\Domain\Contracts\DamagedRepositoryInterface;
use Modules\POS\Domain\Entities\Damaged;
use Modules\POS\Domain\Enums\StockDirection;
use Modules\POS\Domain\Enums\StockModelType;
use Modules\POS\Domain\Events\DamagedRecorded;
use Modules\POS\Domain\Events\StockChanged;
use Modules\POS\Domain\Strategies\Stock\StockOperationStrategyInterface;

class DamagedService
{
    public function __construct(
        private readonly DamagedRepositoryInterface $repository,
        private readonly StockOperationStrategyInterface $decrementStock,
        private readonly StockOperationStrategyInterface $incrementStock,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Damaged
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): Damaged
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['created_by'] = $userId;
            $record = $this->repository->create($data);

            $this->decrementStock->execute(
                productId: $record->product_id,
                branchId:  $record->branch_id,
                amount:    $record->amount,
                model:     StockModelType::Damaged,
                objectId:  $record->id,
                createdBy: $userId,
            );

            StockChanged::dispatch($record->product_id, $record->amount, StockDirection::Decrement, StockModelType::Damaged, $record->id);
            DamagedRecorded::dispatch($record);

            return $record;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->repository->findOrFail($id);

            $this->incrementStock->execute(
                productId: $record->product_id,
                branchId:  $record->branch_id,
                amount:    $record->amount,
                model:     StockModelType::Damaged,
                objectId:  $record->id,
                createdBy: auth()->id() ?? 0,
            );

            return $this->repository->delete($id);
        });
    }
}
