<?php

namespace Modules\Email\Services;

use Modules\Email\DTOs\CreateEmailGroupData;
use Modules\Email\DTOs\UpdateEmailGroupData;
use Modules\Email\Entities\EmailGroup;
use Modules\Email\Repositories\EmailGroupInterface;

class EmailGroupService
{
    protected $repository;
    public $model;

    public function __construct(EmailGroupInterface $repository, EmailGroup $emailGroup)
    {
        $this->model = $emailGroup;
        $this->repository = $repository;
    }

    public function getAll(array $conditions = [])
    {
        return $this->repository->all($conditions);
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function getPaginated()
    {
        return $this->repository->getPaginated();
    }

    public function findOrFail(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function getRecipientsByIds($ids)
    {
        return $this->repository->getRecipientsByIds($ids);
    }

    public function create(CreateEmailGroupData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'description' => $data->description,
        ]);
    }

    public function update($id, UpdateEmailGroupData $data)
    {
        return $this->repository->update($id, $data->toArray());
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }
}
