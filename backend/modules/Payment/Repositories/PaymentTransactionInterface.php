<?php

namespace Modules\Payment\Repositories;

interface PaymentTransactionInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function getByCustomer($customerId);
    public function getByStatus($status);
    public function getByDateRange($startDate, $endDate);
    public function getSuccessful();
    public function getFailed();
    public function getAnalytics($startDate = null, $endDate = null);
}
