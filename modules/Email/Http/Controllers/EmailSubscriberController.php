<?php

namespace Modules\Email\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Email\Services\EmailSubscriberService;
use Illuminate\Http\Request;

class EmailSubscriberController extends ApiController
{
    protected $service;

    public function __construct(EmailSubscriberService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate($this->service->model->pluralTitle);

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];


        return view('landlord.emails.email-subscribers.index', compact('breadcrumbs', 'title',));
    }

    public function create()
    {
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        return view('landlord.emails.email-subscribers.editor', compact('statusOptions'));
    }

    public function store(Request $request) {}

    public function show($id) {}

    public function edit($id)
    {
        $statusOptions = EnumHelper::getEnumFromTable($this->service->model->getTable(), "status");
        $row = $this->service->get($id);
        return view('landlord.emails.email-subscribers.editor', compact('row', 'statusOptions'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return $this->return(200, translate("updated_successfully"));
    }

    public function destroy($id)
    {
    }

    public function restore($id)
    {
    }
}
