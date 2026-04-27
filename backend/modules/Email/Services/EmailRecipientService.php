<?php

namespace Modules\Email\Services;

use Modules\Email\DTOs\CreateEmailRecipientData;
use Modules\Email\DTOs\UpdateEmailRecipientData;
use Modules\Email\Entities\EmailRecipient;
use Modules\Email\Repositories\EmailRecipientInterface;

class EmailRecipientService
{
    protected $repository;
    public $model;

    public function __construct(EmailRecipientInterface $repository, EmailRecipient $emailRecipient)
    {
        $this->model = $emailRecipient;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function count()
    {
        return $this->repository->count();
    }

    public function getPaginated()
    {
        return $this->repository->getPaginated();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function getByEmail($email)
    {
        return $this->repository->getByEmail($email);
    }

    public function create(CreateEmailRecipientData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'email' => $data->email,
            'group_id' => $data->group_id,
        ]);
    }

    public function update($id, UpdateEmailRecipientData $data)
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
