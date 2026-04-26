<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\SendingLog;

use Modules\SmsMarketing\Infrastructure\Persistence\SmSendingLogRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\SendingLog\CreateSmSendingLogDTO;
use Modules\SmsMarketing\Application\DTOs\SendingLog\UpdateSmSendingLogDTO;

class SmSendingLogUseCase
{
    public function __construct(
        private readonly SmSendingLogRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmSendingLogDTO $dto)
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, UpdateSmSendingLogDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function getTableList(array $params)
    {
        return $this->repository->getTableList($params);
    }
}
