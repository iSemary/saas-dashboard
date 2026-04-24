<?php

namespace Modules\CRM\Repositories;

use Illuminate\Support\Facades\DB;

class CrmDashboardRepository implements CrmDashboardRepositoryInterface
{
    public function getDashboardData(): array
    {
        return [
            'contacts_count' => DB::table('contacts')->count(),
            'companies_count' => DB::table('companies')->count(),
            'deals_count' => DB::table('deals')->count(),
            'recent_contacts' => DB::table('contacts')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];
    }
}
