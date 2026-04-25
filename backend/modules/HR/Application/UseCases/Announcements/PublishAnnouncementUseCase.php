<?php

namespace Modules\HR\Application\UseCases\Announcements;

use Modules\HR\Domain\Entities\Announcement;
use Modules\HR\Infrastructure\Persistence\AnnouncementRepositoryInterface;

class PublishAnnouncementUseCase
{
    public function __construct(
        protected AnnouncementRepositoryInterface $repository,
    ) {}

    public function execute(array $data): Announcement
    {
        $data['created_by'] = auth()->id();
        return $this->repository->create($data);
    }
}
