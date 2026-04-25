<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\ExpenseClaim;

class ExpenseClaimRepository implements ExpenseClaimRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = ExpenseClaim::query();
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): ExpenseClaim
    {
        return ExpenseClaim::findOrFail($id);
    }

    public function create(array $data): ExpenseClaim
    {
        return ExpenseClaim::create($data);
    }

    public function update(int $id, array $data): ExpenseClaim
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return ExpenseClaim::destroy($id) > 0;
    }
}
