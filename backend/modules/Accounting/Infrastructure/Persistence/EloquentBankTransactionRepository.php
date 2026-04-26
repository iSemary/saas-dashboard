<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\BankTransaction;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBankTransactionRepository implements BankTransactionRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?BankTransaction
    {
        return BankTransaction::find($id);
    }

    public function findOrFail(int $id): BankTransaction
    {
        return BankTransaction::findOrFail($id);
    }

    public function create(array $data): BankTransaction
    {
        return BankTransaction::create($data);
    }

    public function update(int $id, array $data): BankTransaction
    {
        $model = BankTransaction::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return BankTransaction::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return BankTransaction::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BankTransaction::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return BankTransaction::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return BankTransaction::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = BankTransaction::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
