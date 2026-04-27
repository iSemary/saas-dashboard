<?php

namespace Modules\Development\Services;

use Modules\Development\DTOs\CreateIpBlacklistData;
use Modules\Development\DTOs\UpdateIpBlacklistData;
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

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateIpBlacklistData $data)
    {
        return $this->repository->create([
            'ip_address' => $data->ip_address,
        ]);
    }

    public function update($id, UpdateIpBlacklistData $data)
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
