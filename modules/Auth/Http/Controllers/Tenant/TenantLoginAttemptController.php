<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Helpers\TableHelper;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Auth\Entities\LoginAttempt;
use Yajra\DataTables\DataTables;
use App\Helpers\IconHelper;

class TenantLoginAttemptController extends ApiController
{
    /**
     * Display login attempts for current user or specific user
     */
    public function index(Request $request, int $id = null)
    {
        // Check if it's an AJAX/DataTables request
        if ($request->ajax() && $request->get('table'))
        {
            return $this->getDataTables($id);
        }

        // For non-AJAX requests, return the view
        $userId = $id ?? auth()->id();
        $route = route('tenant.login-attempts.index', $userId);
        
        $title = translate('login_attempts');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('login_attempts')],
        ];

        return view('tenant.auth.login-attempts.index', compact('route', 'title', 'breadcrumbs'));
    }

    /**
     * Get DataTables data for login attempts
     */
    protected function getDataTables(int $id = null)
    {
        $userId = $id ?? auth()->id();
        
        $rows = LoginAttempt::query()
            ->where('user_id', $userId)
            ->when(request()->from_date && request()->to_date, function ($query)
            {
                TableHelper::loopOverDates(5, $query, app(LoginAttempt::class)->getTable(), [request()->from_date, request()->to_date]);
            })
            ->orderBy('created_at', 'desc');

        return DataTables::of($rows)
            ->addColumn('agent', function ($row)
            {
                return IconHelper::formatAgentIcons($row->agent);
            })
            ->rawColumns(['agent'])
            ->make(true);
    }
}

