<?php

namespace Modules\HR\Infrastructure\Persistence;

use Modules\HR\Domain\Entities\PipelineStage;

interface PipelineStageRepositoryInterface
{
    /** @return PipelineStage[] */
    public function getAll(): array;
    public function findOrFail(int $id): PipelineStage;
    public function getDefault(): ?PipelineStage;
    public function create(array $data): PipelineStage;
    public function update(int $id, array $data): PipelineStage;
    public function delete(int $id): bool;
}
