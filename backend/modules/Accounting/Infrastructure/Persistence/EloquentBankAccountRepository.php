<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\BankAccount;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBankAccountRepository implements BankAccountRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?BankAccount
    {
        return BankAccount::find($id);
    }

    public function findOrFail(int $id): BankAccount
    {
        return BankAccount::findOrFail($id);
    }

    public function create(array $data): BankAccount
    {
        return BankAccount::create($data);
    }

    public function update(int $id, array $data): BankAccount
    {
        $model = BankAccount::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return BankAccount::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return BankAccount::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BankAccount::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return BankAccount::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return BankAccount::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = BankAccount::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
