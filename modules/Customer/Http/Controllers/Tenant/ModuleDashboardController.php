<?php

namespace Modules\Customer\Http\Controllers\Tenant;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ModuleDashboardController extends ApiController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:access.modules', only: ['index']),
        ];
    }

    /**
     * HR Module Dashboard
     */
    public function hr()
    {
        $title = translate('hr_dashboard');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('hr_dashboard')],
        ];

        return view('tenant.modules.hr.dashboard', compact('title', 'breadcrumbs'));
    }

    /**
     * CRM Module Dashboard
     */
    public function crm()
    {
        $title = translate('crm_dashboard');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('crm_dashboard')],
        ];

        return view('tenant.modules.crm.dashboard', compact('title', 'breadcrumbs'));
    }

    /**
     * POS Module Dashboard
     */
    public function pos()
    {
        $title = translate('pos_dashboard');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('pos_dashboard')],
        ];

        return view('tenant.modules.pos.dashboard', compact('title', 'breadcrumbs'));
    }

    /**
     * Accounting Module Dashboard
     */
    public function accounting()
    {
        $title = translate('accounting_dashboard');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('accounting_dashboard')],
        ];

        return view('tenant.modules.accounting.dashboard', compact('title', 'breadcrumbs'));
    }

    /**
     * Sales Module Dashboard
     */
    public function sales()
    {
        $title = translate('sales_dashboard');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('sales_dashboard')],
        ];

        return view('tenant.modules.sales.dashboard', compact('title', 'breadcrumbs'));
    }

    /**
     * Inventory Module Dashboard
     */
    public function inventory()
    {
        $title = translate('inventory_dashboard');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('inventory_dashboard')],
        ];

        return view('tenant.modules.inventory.dashboard', compact('title', 'breadcrumbs'));
    }
}
