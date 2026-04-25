<?php

namespace Modules\HR\Infrastructure\Persistence;

use Modules\HR\Domain\Entities\PipelineStage;

class PipelineStageRepository implements PipelineStageRepositoryInterface
{
    public function getAll(): array
    {
        return PipelineStage::ordered()->get()->toArray();
    }

    public function findOrFail(int $id): PipelineStage
    {
        return PipelineStage::findOrFail($id);
    }

    public function getDefault(): ?PipelineStage
    {
        return PipelineStage::default()->first();
    }

    public function create(array $data): PipelineStage
    {
        return PipelineStage::create($data);
    }

    public function update(int $id, array $data): PipelineStage
    {
        $stage = $this->findOrFail($id);
        $stage->update($data);
        return $stage->fresh();
    }

    public function delete(int $id): bool
    {
        return PipelineStage::destroy($id) > 0;
    }
}
