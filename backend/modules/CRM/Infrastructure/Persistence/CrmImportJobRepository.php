<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmImportJob;

class CrmImportJobRepository implements CrmImportJobRepositoryInterface
{
    public function __construct(protected CrmImportJob $model) {}
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator { return $this->model->with(['creator'])->paginate($perPage); }
    public function findOrFail(int $id): CrmImportJob { return $this->model->findOrFail($id); }
    public function create(array $data): CrmImportJob { return $this->model->create($data); }
    public function update(int $id, array $data): CrmImportJob { $job = $this->findOrFail($id); $job->update($data); return $job; }
    public function delete(int $id): bool { return $this->model->destroy($id) > 0; }
    public function getPending(): Collection { return $this->model->pending()->get(); }
    public function getProcessing(): Collection { return $this->model->processing()->get(); }
    public function getByUser(int $userId): Collection { return $this->model->where('created_by', $userId)->get(); }
}
