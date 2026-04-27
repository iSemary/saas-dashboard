<?php

namespace Modules\Payment\Services;

use Modules\Payment\DTOs\CreatePaymentMethodData;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Repositories\PaymentMethodInterface;

class PaymentMethodService
{
    protected $repository;
    public $model;

    public function __construct(PaymentMethodInterface $repository, PaymentMethod $paymentMethod)
    {
        $this->model = $paymentMethod;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreatePaymentMethodData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'slug' => $data->slug,
            'is_active' => $data->is_active ?? true,
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
