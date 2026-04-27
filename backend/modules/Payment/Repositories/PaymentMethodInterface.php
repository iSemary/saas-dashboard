<?php

namespace Modules\Payment\Repositories;

interface PaymentMethodInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function getActive();
    public function getByProcessor($processorType);
    public function getAvailableForCountry($countryCode);
    public function getAvailableForCurrency($currencyCode);
}
