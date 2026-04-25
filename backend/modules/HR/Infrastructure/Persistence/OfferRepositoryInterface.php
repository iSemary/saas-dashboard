<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Offer;

interface OfferRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Offer;
    public function create(array $data): Offer;
    public function update(int $id, array $data): Offer;
    public function delete(int $id): bool;
}
