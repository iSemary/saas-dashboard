<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Activity;

use Modules\CRM\Domain\Entities\Activity;
use Modules\CRM\Domain\Events\ActivityCreated;
use Modules\CRM\Infrastructure\Persistence\ActivityRepositoryInterface;

class CreateActivityUseCase
{
    public function __construct(private readonly ActivityRepositoryInterface $activities) {}

    public function execute(array $data, int $userId): Activity
    {
        $data['created_by'] = $userId;
        $activity = $this->activities->create($data);
        event(new ActivityCreated($activity, $data));
        return $activity;
    }
}
