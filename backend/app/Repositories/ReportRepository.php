<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    public function getCustomers(array $filters): \Illuminate\Support\Collection
    {
        $query = DB::table('companies')->where('type', 'customer');

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    public function getTickets(array $filters): \Illuminate\Support\Collection
    {
        $query = DB::table('tickets');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }
}
