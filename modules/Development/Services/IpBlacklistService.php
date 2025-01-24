<?php

namespace Modules\Development\Services;

use Modules\Development\Entities\IpBlacklist;
use Modules\Development\Repositories\IpBlacklistInterface;

class IpBlacklistService
{
    protected $repository;
    public $model;

    public function __construct(IpBlacklistInterface $repository, IpBlacklist $ip_blacklist)
    {
        $this->model = $ip_blacklist;
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
