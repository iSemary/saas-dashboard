<?php

namespace Modules\POS\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\POS\Domain\Entities\OfferPrice;

interface OfferPriceRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): OfferPrice;
    public function create(array $data): OfferPrice;
    public function update(int $id, array $data): OfferPrice;
    public function delete(int $id): bool;
}
