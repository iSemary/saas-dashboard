<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\SendingLog;

use Modules\EmailMarketing\Infrastructure\Persistence\EmSendingLogRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\SendingLog\CreateEmSendingLogDTO;
use Modules\EmailMarketing\Application\DTOs\SendingLog\UpdateEmSendingLogDTO;

class EmSendingLogUseCase
{
    public function __construct(
        private readonly EmSendingLogRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmSendingLogDTO $dto)
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, UpdateEmSendingLogDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function getTableList(array $params)
    {
        return $this->repository->getTableList($params);
    }
}
