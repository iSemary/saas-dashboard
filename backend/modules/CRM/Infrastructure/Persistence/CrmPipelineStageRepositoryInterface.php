<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmPipelineStage;

interface CrmPipelineStageRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): CrmPipelineStage;
    public function findByKey(string $key): ?CrmPipelineStage;
    public function create(array $data): CrmPipelineStage;
    public function update(int $id, array $data): CrmPipelineStage;
    public function delete(int $id): bool;
    public function getOrdered(): Collection;
    public function getDefault(): ?CrmPipelineStage;
}
