<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Auth\Services\ActivityLogService;

class ActivityLogController extends ApiController
{
    protected $service;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->service = $activityLogService;
    }

    public function index(Request $request, int $id = null)
    {
        $title = translate("activity_logs");

        $layoutPrefix = auth()->user()->getCurrentTypeName();

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("activity_logs")],
        ];

        // User details
        /***
            Name, email, id, created at
            Who created this user
         */

        /**
         *  Modules
         *      - Entities of each module
         */

        /**
         * Each entity as a tab viewing the the changes as a datatable
         */

        $page = $request->get('page', 1);
        $type = $request->get('type', null);

        $result = $this->service->getTimelineData($id ?? auth()->id(), $page, $type);

        return view('user.auth.activity-logs.index', [
            'id' => $id,
            'title' => $title,
            'layoutPrefix' => $layoutPrefix,
            'breadcrumbs' => $breadcrumbs,


            'activities' => $result['activities'],
            'pagination' => $result['pagination']        ]);
    }

    public function modal(Request $request, int $id = null)
    {
        if (request()->ajax() && request()->get('table')) {
            return $this->service->getDataTables($id);
        }
        return view('user.auth.activity-logs.modal', ['id' => $id]);
    }
}
