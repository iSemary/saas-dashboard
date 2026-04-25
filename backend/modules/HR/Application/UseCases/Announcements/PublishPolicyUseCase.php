<?php

namespace Modules\HR\Application\UseCases\Announcements;

use Modules\HR\Domain\Entities\Policy;
use Modules\HR\Infrastructure\Persistence\PolicyRepositoryInterface;

class PublishPolicyUseCase
{
    public function __construct(
        protected PolicyRepositoryInterface $repository,
    ) {}

    public function execute(array $data): Policy
    {
        $data['created_by'] = auth()->id();
        return $this->repository->create($data);
    }
}
