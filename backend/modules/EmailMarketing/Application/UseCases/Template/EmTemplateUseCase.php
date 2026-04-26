<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\Template;

use Modules\EmailMarketing\Infrastructure\Persistence\EmTemplateRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\Template\CreateEmTemplateDTO;
use Modules\EmailMarketing\Application\DTOs\Template\UpdateEmTemplateDTO;

class EmTemplateUseCase
{
    public function __construct(
        private readonly EmTemplateRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmTemplateDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateEmTemplateDTO $dto)
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
