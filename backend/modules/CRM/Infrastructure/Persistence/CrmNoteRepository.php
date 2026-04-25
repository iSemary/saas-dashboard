<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmNote;

class CrmNoteRepository implements CrmNoteRepositoryInterface
{
    public function __construct(protected CrmNote $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['creator']);

        if (isset($filters['related_type']) && isset($filters['related_id'])) {
            $query->forRelated($filters['related_type'], $filters['related_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): CrmNote
    {
        return $this->model->with(['creator', 'related'])->findOrFail($id);
    }

    public function create(array $data): CrmNote
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): CrmNote
    {
        $note = $this->findOrFail($id);
        $note->update($data);
        return $note;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function getForRelated(string $type, int $id): Collection
    {
        return $this->model->forRelated($type, $id)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
