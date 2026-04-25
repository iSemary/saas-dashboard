<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Shared\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSurveyRepository implements SurveyRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?Survey
    {
        return Survey::find($id);
    }

    public function findOrFail(int $id): Survey
    {
        return Survey::findOrFail($id);
    }

    public function create(array $data): Survey
    {
        return Survey::create($data);
    }

    public function update(int $id, array $data): Survey
    {
        $survey = $this->findOrFail($id);
        $survey->update($data);
        return $survey->fresh();
    }

    public function delete(int $id): bool
    {
        $survey = $this->find($id);
        return $survey ? $survey->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Survey::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        $query = Survey::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('title')->get()->toArray();
    }

    public function exists(int $id): bool
    {
        return Survey::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = Survey::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->count();
    }

    public function getTableList(array $params): array
    {
        $query = Survey::query()
            ->with(['creator', 'theme'])
            ->select([
                'id',
                'title',
                'status',
                'created_by',
                'theme_id',
                'created_at',
                'updated_at',
            ]);

        return $this->getList($query, $params);
    }
}
