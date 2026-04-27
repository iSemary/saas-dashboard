<?php

namespace Modules\Geography\Services;

use Modules\Geography\DTOs\CreateCountryData;
use Modules\Geography\DTOs\UpdateCountryData;
use Modules\Geography\Entities\Country;
use Modules\Geography\Repositories\CountryInterface;

class CountryService
{
    protected $repository;
    public $model;

    public function __construct(CountryInterface $repository, Country $country)
    {
        $this->model = $country;
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

    public function getTimeZones () {
        return $this->repository->getTimeZones();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateCountryData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'code' => $data->code,
            'phone_code' => $data->phone_code,
            'is_active' => $data->is_active ?? true,
        ]);
    }

    public function update($id, UpdateCountryData $data)
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

