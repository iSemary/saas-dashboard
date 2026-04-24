<?php

namespace Modules\Utilities\Services;

use Modules\Utilities\DTOs\CreateIndustryData;
use Modules\Utilities\Entities\Industry;
use Modules\Utilities\Repositories\IndustryInterface;

class IndustryService
{
    protected $repository;
    public $model;

    public function __construct(IndustryInterface $repository, Industry $industry)
    {
        $this->model = $industry;
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

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateIndustryData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'slug' => $data->slug,
        ]);
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

