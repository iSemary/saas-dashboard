<?php

namespace App\Repositories;

interface ReportRepositoryInterface
{
    public function getCustomers(array $filters): \Illuminate\Support\Collection;

    public function getTickets(array $filters): \Illuminate\Support\Collection;
}
