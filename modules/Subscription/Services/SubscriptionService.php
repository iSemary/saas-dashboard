<?php

namespace Modules\Subscription\Services;

use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Repositories\SubscriptionInterface;

class SubscriptionService
{
    protected $repository;
    public $model;

    public function __construct(SubscriptionInterface $repository, Subscription $plan)
    {
        $this->model = $plan;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
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

