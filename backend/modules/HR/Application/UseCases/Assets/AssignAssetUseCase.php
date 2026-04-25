<?php

namespace Modules\HR\Application\UseCases\Assets;

use Modules\HR\Domain\Entities\AssetAssignment;
use Modules\HR\Infrastructure\Persistence\AssetAssignmentRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AssetRepositoryInterface;

class AssignAssetUseCase
{
    public function __construct(
        protected AssetAssignmentRepositoryInterface $assignmentRepository,
        protected AssetRepositoryInterface $assetRepository,
    ) {}

    public function execute(array $data): AssetAssignment
    {
        $assignment = $this->assignmentRepository->create($data + ['assigned_at' => ($data['assigned_at'] ?? now())]);
        $this->assetRepository->update((int) $data['asset_id'], ['status' => 'assigned']);
        return $assignment;
    }
}
