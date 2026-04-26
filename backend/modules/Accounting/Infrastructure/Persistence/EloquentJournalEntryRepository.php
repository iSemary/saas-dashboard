<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\JournalEntry;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentJournalEntryRepository implements JournalEntryRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?JournalEntry
    {
        return JournalEntry::with(['journalItems.account', 'fiscalYear', 'creator'])->find($id);
    }

    public function findOrFail(int $id): JournalEntry
    {
        return JournalEntry::with(['journalItems.account', 'fiscalYear', 'creator', 'poster'])->findOrFail($id);
    }

    public function create(array $data): JournalEntry
    {
        return JournalEntry::create($data);
    }

    public function update(int $id, array $data): JournalEntry
    {
        $model = JournalEntry::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return JournalEntry::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return JournalEntry::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = JournalEntry::with(['journalItems.account', 'fiscalYear', 'creator']);

        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('entry_date', [$filters['from_date'], $filters['to_date']]);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('entry_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('reference', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('entry_date', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        $query = JournalEntry::query();

        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        return $query->orderBy('entry_date', 'desc')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        $query = JournalEntry::query();

        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        return $query->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = JournalEntry::query()
            ->with(['creator'])
            ->select([
                'id', 'entry_number', 'entry_date', 'state', 'reference',
                'total_debit', 'total_credit', 'created_by', 'created_at',
            ]);

        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function sumPosted(string $column): float
    {
        return (float) JournalEntry::where('state', 'posted')->sum($column);
    }
}
