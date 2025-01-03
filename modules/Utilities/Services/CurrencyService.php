<?php

namespace Modules\Utilities\Services;

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
}
