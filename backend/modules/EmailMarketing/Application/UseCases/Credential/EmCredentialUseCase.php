<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\Credential;

use Modules\EmailMarketing\Infrastructure\Persistence\EmCredentialRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\Credential\CreateEmCredentialDTO;
use Modules\EmailMarketing\Application\DTOs\Credential\UpdateEmCredentialDTO;

class EmCredentialUseCase
{
    public function __construct(
        private readonly EmCredentialRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmCredentialDTO $dto)
    {
        $data = $dto->toArray();

        if (isset($data['password'])) {
            $data['password'] = encrypt($data['password']);
        }

        return $this->repository->create(array_merge($data, ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateEmCredentialDTO $dto)
    {
        $data = $dto->toArray();

        if (isset($data['password'])) {
            $data['password'] = encrypt($data['password']);
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
