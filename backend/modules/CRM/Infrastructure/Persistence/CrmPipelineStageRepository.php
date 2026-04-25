<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmPipelineStage;

class CrmPipelineStageRepository implements CrmPipelineStageRepositoryInterface
{
    public function __construct(protected CrmPipelineStage $model) {}
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator { return $this->model->ordered()->paginate($perPage); }
    public function findOrFail(int $id): CrmPipelineStage { return $this->model->findOrFail($id); }
    public function findByKey(string $key): ?CrmPipelineStage { return $this->model->where('key', $key)->first(); }
    public function create(array $data): CrmPipelineStage { return $this->model->create($data); }
    public function update(int $id, array $data): CrmPipelineStage { $stage = $this->findOrFail($id); $stage->update($data); return $stage; }
    public function delete(int $id): bool { return $this->model->destroy($id) > 0; }
    public function getOrdered(): Collection { return $this->model->ordered()->get(); }
    public function getDefault(): ?CrmPipelineStage { return $this->model->default()->first(); }
}
