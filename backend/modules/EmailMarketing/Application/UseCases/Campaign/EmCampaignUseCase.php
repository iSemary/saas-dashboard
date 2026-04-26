<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\Campaign;

use Modules\EmailMarketing\Infrastructure\Persistence\EmCampaignRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmContactListRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\Campaign\CreateEmCampaignDTO;
use Modules\EmailMarketing\Application\DTOs\Campaign\UpdateEmCampaignDTO;
use Modules\EmailMarketing\Domain\ValueObjects\EmCampaignStatus;
use Modules\EmailMarketing\Domain\Strategies\Sending\EmailSendingStrategyInterface;

class EmCampaignUseCase
{
    public function __construct(
        private readonly EmCampaignRepositoryInterface $repository,
        private readonly EmContactListRepositoryInterface $contactListRepo,
        private readonly EmailSendingStrategyInterface $sendingStrategy,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmCampaignDTO $dto)
    {
        $campaign = $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));

        if ($dto->contact_list_ids) {
            $campaign->contactLists()->sync($dto->contact_list_ids);
        }

        return $campaign->fresh();
    }

    public function update(int $id, UpdateEmCampaignDTO $dto)
    {
        $campaign = $this->repository->findOrFail($id);

        if (! $campaign->isEditable()) {
            throw new \RuntimeException('Cannot update a campaign that is sending, sent, or cancelled');
        }

        $this->repository->update($id, $dto->toArray());

        if ($dto->contact_list_ids !== null) {
            $campaign->contactLists()->sync($dto->contact_list_ids);
        }

        return $campaign->fresh();
    }

    public function delete(int $id): bool
    {
        $campaign = $this->repository->findOrFail($id);

        if (! $campaign->isEditable()) {
            throw new \RuntimeException('Cannot delete a campaign that is sending or sent');
        }

        return $this->repository->delete($id);
    }

    public function send(int $id): void
    {
        $campaign = $this->repository->findOrFail($id);
        $campaign->transitionTo(EmCampaignStatus::Sending);
        $this->sendingStrategy->send($campaign);
        $campaign->transitionTo(EmCampaignStatus::Sent);
    }

    public function schedule(int $id, string $scheduledAt): void
    {
        $campaign = $this->repository->findOrFail($id);
        $campaign->transitionTo(EmCampaignStatus::Scheduled);
        $campaign->update(['scheduled_at' => $scheduledAt]);
    }

    public function pause(int $id): void
    {
        $campaign = $this->repository->findOrFail($id);
        $campaign->transitionTo(EmCampaignStatus::Paused);
    }

    public function cancel(int $id): void
    {
        $campaign = $this->repository->findOrFail($id);
        $campaign->transitionTo(EmCampaignStatus::Cancelled);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    public function getTableList(array $params)
    {
        return $this->repository->getTableList($params);
    }
}
