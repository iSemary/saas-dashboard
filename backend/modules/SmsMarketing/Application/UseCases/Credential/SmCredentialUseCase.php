<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\Credential;

use Modules\SmsMarketing\Infrastructure\Persistence\SmCredentialRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\Credential\CreateSmCredentialDTO;
use Modules\SmsMarketing\Application\DTOs\Credential\UpdateSmCredentialDTO;

class SmCredentialUseCase
{
    public function __construct(
        private readonly SmCredentialRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmCredentialDTO $dto)
    {
        $data = $dto->toArray();

        if (isset($data['auth_token'])) {
            $data['auth_token'] = encrypt($data['auth_token']);
        }

        return $this->repository->create(array_merge($data, ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateSmCredentialDTO $dto)
    {
        $data = $dto->toArray();

        if (isset($data['auth_token'])) {
            $data['auth_token'] = encrypt($data['auth_token']);
        }

        return $this->repository->update($id, $data);
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
