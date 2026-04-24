<?php

namespace Modules\Subscription\Services;

use Modules\Subscription\Entities\Plan;
use Modules\Subscription\Repositories\PlanInterface;

class PlanService
{
    protected $repository;
    public $model;

    public function __construct(PlanInterface $repository, Plan $plan)
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

    public function getActive()
    {
        return $this->repository->getActive();
    }

    public function getPopular()
    {
        return $this->repository->getPopular();
    }

    public function getBySlug($slug)
    {
        return $this->repository->getBySlug($slug);
    }

    public function getWithPricing($currencyCode = 'USD', $countryCode = null)
    {
        return $this->repository->getWithPricing($currencyCode, $countryCode);
    }

    public function getAvailableUpgrades($planId)
    {
        return $this->repository->getAvailableUpgrades($planId);
    }

    public function getAvailableDowngrades($planId)
    {
        return $this->repository->getAvailableDowngrades($planId);
    }
}
