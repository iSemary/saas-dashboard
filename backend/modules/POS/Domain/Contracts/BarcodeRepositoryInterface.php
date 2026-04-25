<?php

namespace Modules\POS\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Entities\Barcode;

interface BarcodeRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findByNumber(string $barcodeNumber): ?Barcode;
    public function findOrFail(int $id): Barcode;
    public function create(array $data): Barcode;
    public function delete(int $id): bool;
}
