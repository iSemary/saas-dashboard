<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\Webhook;

use Modules\EmailMarketing\Infrastructure\Persistence\EmWebhookRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\Webhook\CreateEmWebhookDTO;
use Modules\EmailMarketing\Application\DTOs\Webhook\UpdateEmWebhookDTO;

class EmWebhookUseCase
{
    public function __construct(
        private readonly EmWebhookRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmWebhookDTO $dto)
    {
        $data = $dto->toArray();

        if (empty($data['secret'])) {
            $data['secret'] = bin2hex(random_bytes(32));
        }

        return $this->repository->create(array_merge($data, ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateEmWebhookDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
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
