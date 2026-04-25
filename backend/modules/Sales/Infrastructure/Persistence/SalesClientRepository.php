<?php

namespace Modules\Sales\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Sales\Domain\Contracts\SalesClientRepositoryInterface;
use Modules\Sales\Domain\Entities\SalesClient;

class SalesClientRepository implements SalesClientRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SalesClient::with(['user', 'creator']);

        if (!empty($filters['search'])) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$filters['search']}%"))
                  ->orWhere('code', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): SalesClient
    {
        return SalesClient::with(['user', 'orders'])->findOrFail($id);
    }

    public function findByUserId(int $userId): ?SalesClient
    {
        return SalesClient::where('user_id', $userId)->first();
    }

    public function create(array $data): SalesClient
    {
        return SalesClient::create($data);
    }

    public function update(int $id, array $data): SalesClient
    {
        $client = SalesClient::findOrFail($id);
        $client->update($data);
        return $client->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) SalesClient::findOrFail($id)->delete();
    }
}
