<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmTemplate;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmTemplateRepository implements EmTemplateRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmTemplate
    {
        return EmTemplate::find($id);
    }

    public function findOrFail(int $id): EmTemplate
    {
        return EmTemplate::findOrFail($id);
    }

    public function create(array $data): EmTemplate
    {
        return EmTemplate::create($data);
    }

    public function update(int $id, array $data): EmTemplate
    {
        $model = EmTemplate::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmTemplate::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmTemplate::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmTemplate::query();

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
        $query = EmTemplate::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'subject' => 'subject']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return EmTemplate::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return EmTemplate::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

}
