<?php

namespace Modules\HR\Tests\Unit;

use Modules\HR\Application\UseCases\Assets\AssignAssetUseCase;
use Modules\HR\Domain\Entities\AssetAssignment;
use Modules\HR\Infrastructure\Persistence\AssetAssignmentRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AssetRepositoryInterface;
use PHPUnit\Framework\TestCase;

class AssignAssetUseCaseTest extends TestCase
{
    public function test_creates_assignment_and_marks_asset_assigned(): void
    {
        $assignmentRepo = $this->createMock(AssetAssignmentRepositoryInterface::class);
        $assetRepo = $this->createMock(AssetRepositoryInterface::class);

        $assignmentRepo->expects($this->once())
            ->method('create')
            ->willReturn(new AssetAssignment());

        $assetRepo->expects($this->once())
            ->method('update')
            ->with(10, ['status' => 'assigned']);

        $useCase = new AssignAssetUseCase($assignmentRepo, $assetRepo);
        $result = $useCase->execute([
            'asset_id' => 10,
            'employee_id' => 20,
        ]);

        $this->assertInstanceOf(AssetAssignment::class, $result);
    }
}
