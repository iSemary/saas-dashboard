<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmContact;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmContactRepository implements SmContactRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmContact
    {
        return SmContact::find($id);
    }

    public function findOrFail(int $id): SmContact
    {
        return SmContact::findOrFail($id);
    }

    public function create(array $data): SmContact
    {
        return SmContact::create($data);
    }

    public function update(int $id, array $data): SmContact
    {
        $model = SmContact::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmContact::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmContact::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmContact::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = SmContact::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['phone' => 'phone', 'first_name' => 'first_name', 'last_name' => 'last_name']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return SmContact::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return SmContact::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function sum(string $column): float
    {
        return (float) SmContact::query()->sum($column);
    }

}
