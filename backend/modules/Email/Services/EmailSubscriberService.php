<?php

namespace Modules\Email\Services;

use Modules\Email\DTOs\CreateEmailSubscriberData;
use Modules\Email\DTOs\UpdateEmailSubscriberData;
use Modules\Email\Entities\EmailSubscriber;
use Modules\Email\Repositories\EmailSubscriberInterface;

class EmailSubscriberService
{
    protected $repository;
    public $model;

    public function __construct(EmailSubscriberInterface $repository, EmailSubscriber $emailSubscriber)
    {
        $this->model = $emailSubscriber;
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

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateEmailSubscriberData $data)
    {
        return $this->repository->create([
            'email' => $data->email,
            'name' => $data->name,
            'is_active' => $data->is_active ?? true,
        ]);
    }

    public function update($id, UpdateEmailSubscriberData $data)
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
