<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\Campaign;

use Modules\SmsMarketing\Infrastructure\Persistence\SmCampaignRepositoryInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmContactListRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\Campaign\CreateSmCampaignDTO;
use Modules\SmsMarketing\Application\DTOs\Campaign\UpdateSmCampaignDTO;
use Modules\SmsMarketing\Domain\ValueObjects\SmCampaignStatus;
use Modules\SmsMarketing\Domain\Strategies\Sending\SmsSendingStrategyInterface;

class SmCampaignUseCase
{
    public function __construct(
        private readonly SmCampaignRepositoryInterface $repository,
        private readonly SmContactListRepositoryInterface $contactListRepo,
        private readonly SmsSendingStrategyInterface $sendingStrategy,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmCampaignDTO $dto)
    {
        $campaign = $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));

        if ($dto->contact_list_ids) {
            $campaign->contactLists()->sync($dto->contact_list_ids);
        }

        return $campaign->fresh();
    }

    public function update(int $id, UpdateSmCampaignDTO $dto)
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
        $campaign->transitionTo(SmCampaignStatus::Sending);
        $this->sendingStrategy->send($campaign);
        $campaign->transitionTo(SmCampaignStatus::Sent);
    }

    public function schedule(int $id, string $scheduledAt): void
    {
        $campaign = $this->repository->findOrFail($id);
        $campaign->transitionTo(SmCampaignStatus::Scheduled);
        $campaign->update(['scheduled_at' => $scheduledAt]);
    }

    public function pause(int $id): void
    {
        $campaign = $this->repository->findOrFail($id);
        $campaign->transitionTo(SmCampaignStatus::Paused);
    }

    public function cancel(int $id): void
    {
        $campaign = $this->repository->findOrFail($id);
        $campaign->transitionTo(SmCampaignStatus::Cancelled);
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
