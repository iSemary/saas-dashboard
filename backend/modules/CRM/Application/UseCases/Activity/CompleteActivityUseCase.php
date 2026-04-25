<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Activity;

use Modules\CRM\Domain\Entities\Activity;
use Modules\CRM\Infrastructure\Persistence\ActivityRepositoryInterface;

class CompleteActivityUseCase
{
    public function __construct(private readonly ActivityRepositoryInterface $activities) {}

    public function execute(int $id, ?string $outcome = null, int $userId): Activity
    {
        $activity = $this->activities->complete($id, $outcome);
        return $activity;
    }
}
