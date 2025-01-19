<?php

namespace Modules\Auth\Services;

use Modules\Auth\Repositories\ActivityLogInterface;
use OwenIt\Auditing\Models\Audit;

class ActivityLogService
{
    protected $repository;
    public $model;

    public function __construct(ActivityLogInterface $repository, Audit $audit)
    {
        $this->model = $audit;
        $this->repository = $repository;
    }
    
    public function getDataTables($id)
    {
        return $this->repository->datatables($id);
    }

    public function getById()
    {
        return $this->repository->getById();
    }

    public function getByAuth()
    {
        return $this->repository->getByAuth();
    }
}
