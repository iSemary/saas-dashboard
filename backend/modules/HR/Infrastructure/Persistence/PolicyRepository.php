<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Policy;

class PolicyRepository implements PolicyRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Policy::query()->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Policy
    {
        return Policy::findOrFail($id);
    }

    public function create(array $data): Policy
    {
        return Policy::create($data);
    }

    public function update(int $id, array $data): Policy
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return Policy::destroy($id) > 0;
    }
}
