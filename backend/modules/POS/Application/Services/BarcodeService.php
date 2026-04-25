<?php

namespace Modules\POS\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Contracts\BarcodeRepositoryInterface;
use Modules\POS\Domain\Entities\Barcode;
use Modules\POS\Domain\Entities\Product;

class BarcodeService
{
    public function __construct(
        private readonly BarcodeRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Barcode
    {
        return $this->repository->findOrFail($id);
    }

    public function searchByNumber(string $barcodeNumber): ?Barcode
    {
        return $this->repository->findByNumber($barcodeNumber);
    }

    public function create(array $data, int $userId): Barcode
    {
        return $this->repository->create(array_merge($data, ['created_by' => $userId]));
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
