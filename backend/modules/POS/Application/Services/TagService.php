<?php

namespace Modules\POS\Application\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Domain\Contracts\TagRepositoryInterface;
use Modules\POS\Domain\Entities\Tag;

class TagService
{
    public function __construct(
        private readonly TagRepositoryInterface $repository,
    ) {}

    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function findOrFail(int $id): Tag
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): Tag
    {
        return $this->repository->create(array_merge($data, ['created_by' => $userId]));
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
