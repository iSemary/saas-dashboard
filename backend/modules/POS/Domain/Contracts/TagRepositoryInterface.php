<?php

namespace Modules\POS\Domain\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Domain\Entities\Tag;

interface TagRepositoryInterface
{
    public function all(array $filters = []): Collection;
    public function findOrFail(int $id): Tag;
    public function create(array $data): Tag;
    public function delete(int $id): bool;
}
