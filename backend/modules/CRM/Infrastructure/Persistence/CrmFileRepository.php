<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmFile;

class CrmFileRepository implements CrmFileRepositoryInterface
{
    public function __construct(protected CrmFile $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['creator']);
        if (isset($filters['related_type']) && isset($filters['related_id'])) {
            $query->forRelated($filters['related_type'], $filters['related_id']);
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): CrmFile
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): CrmFile
    {
        return $this->model->create($data);
    }

    public function delete(int $id): bool
    {
        $file = $this->findOrFail($id);
        $file->remove();
        return true;
    }

    public function getForRelated(string $type, int $id): Collection
    {
        return $this->model->forRelated($type, $id)->orderBy('created_at', 'desc')->get();
    }
}
