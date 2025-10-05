<?php

namespace Modules\Auth\Http\Controllers\Tenant;

use App\Helpers\CryptHelper;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Auth\Services\ActivityLogService;

class TenantActivityLogController extends ApiController
{
    protected $service;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->service = $activityLogService;
    }

    /**
     * Display activity logs timeline
     */
    public function index(Request $request, int $id = null)
    {
        $title = translate("activity_logs");
        
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("activity_logs")],
        ];

        $page = $request->get('page', 1);
        $type = $request->get('type', null);

        $result = $this->service->getTimelineData($id ?? auth()->id(), $page, $type);

        return view('tenant.auth.activity-logs.index', [
            'id' => $id,
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'activities' => $result['activities'],
            'pagination' => $result['pagination']
        ]);
    }

    /**
     * Display activity logs modal with datatable
     */
    public function modal(Request $request, int $id = null)
    {
        if ($request->ajax() && $request->get('table'))
        {
            return $this->service->getDataTables($id);
        }
        
        return view('tenant.auth.activity-logs.modal', ['id' => $id]);
    }

    /**
     * Display activity logs for specific row/entity
     */
    public function row(Request $request, int $id = null)
    {
        $objectType = $request->get('object_type');
        $model = CryptHelper::decrypt($objectType);

        if ($request->ajax() && $request->get('table'))
        {
            return $this->service->getDataTablesByRow($id, $model);
        }
        
        return view('tenant.auth.activity-logs.row', ['id' => $id, 'objectType' => $objectType]);
    }
}

