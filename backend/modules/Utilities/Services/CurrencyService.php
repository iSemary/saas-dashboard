<?php

namespace Modules\Utilities\Services;

use Modules\Utilities\DTOs\CreateCurrencyData;
use Modules\Utilities\DTOs\UpdateCurrencyData;
use Modules\Utilities\Entities\Currency;
use Modules\Utilities\Repositories\CurrencyInterface;

class CurrencyService
{
    protected $repository;
    public $model;

    public function __construct(CurrencyInterface $repository, Currency $currency)
    {
        $this->model = $currency;
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

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateCurrencyData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'code' => $data->code,
            'symbol' => $data->symbol,
            'is_active' => $data->is_active,
        ]);
    }

    public function update($id, UpdateCurrencyData $data)
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

