<?php

namespace Modules\Payment\Repositories;

use App\Helpers\TableHelper;
use Modules\Payment\Entities\PaymentMethod;
class PaymentMethodRepository implements PaymentMethodInterface
{
    protected $model;

    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->model = $paymentMethod;
    }

    public function all()
    {
        return $this->model->with(['currencies', 'fees', 'limits'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['currencies', 'fees', 'limits', 'configurations'])->find($id);
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'active';
        $data['is_global'] = isset($data['is_global']) && $data['is_global'] ? true : false;
        $data['priority'] = $data['priority'] ?? 0;
        $data['success_rate'] = 0;
        $data['average_processing_time'] = 0;

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $paymentMethod = $this->find($id);
        
        if (!$paymentMethod) {
            return false;
        }

        $data['is_global'] = isset($data['is_global']) && $data['is_global'] ? true : false;

        return $paymentMethod->update($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)?->delete();
    }

    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)?->restore();
    }

    public function getActive()
    {
        return $this->model->active()->orderBy('priority', 'desc')->get();
    }

    public function getByProcessor($processorType)
    {
        return $this->model->byProcessor($processorType)->get();
    }

    public function getAvailableForCountry($countryCode)
    {
        return $this->model->active()
                          ->where(function ($query) use ($countryCode) {
                              $query->where('is_global', true)
                                    ->orWhereJsonContains('country_codes', $countryCode);
                          })
                          ->get();
    }

    public function getAvailableForCurrency($currencyCode)
    {
        return $this->model->active()
                          ->whereJsonContains('supported_currencies', $currencyCode)
                          ->get();
    }
}
