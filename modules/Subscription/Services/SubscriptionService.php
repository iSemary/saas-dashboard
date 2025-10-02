<?php

namespace Modules\Subscription\Services;

use Modules\Subscription\Entities\PlanSubscription;
use Modules\Subscription\Repositories\SubscriptionInterface;

class SubscriptionService
{
    protected $repository;
    public $model;

    public function __construct(SubscriptionInterface $repository, PlanSubscription $subscription)
    {
        $this->model = $subscription;
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

    public function getByUser($userId)
    {
        return $this->repository->getByUser($userId);
    }

    public function getActiveByUser($userId)
    {
        return $this->repository->getActiveByUser($userId);
    }

    public function getDueForBilling()
    {
        return $this->repository->getDueForBilling();
    }

    public function getExpiringSoon($days = 7)
    {
        return $this->repository->getExpiringSoon($days);
    }

    public function getByStatus($status)
    {
        return $this->repository->getByStatus($status);
    }

    public function getTrialSubscriptions()
    {
        return $this->repository->getTrialSubscriptions();
    }
}
