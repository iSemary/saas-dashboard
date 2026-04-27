<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Development\Services\IpBlacklistService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class IpBlacklistController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(IpBlacklistService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $title = translate($this->service->model->pluralTitle);

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.development.ip-blacklists.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.developments.ip-blacklists.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        return view('landlord.developments.ip-blacklists.editor');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return $this->return(200, translate("created_successfully"));
    }

    public function show($id) {}

    public function edit($id)
    {
        $row = $this->service->get($id);
        return view('landlord.developments.ip-blacklists.editor', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return $this->return(200, translate("updated_successfully"));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, "Deleted successfully");
    }
    
    public function restore($id)
    {
        $this->service->restore($id);
        return $this->return(200, "Deleted successfully");
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.ip_blacklists', only: ['index', 'show']),
            new Middleware('permission:create.ip_blacklists', only: ['create', 'store']),
            new Middleware('permission:update.ip_blacklists', only: ['edit', 'update']),
            new Middleware('permission:delete.ip_blacklists', only: ['destroy']),
            new Middleware('permission:restore.ip_blacklists', only: ['restore']),
        ];
    }
}
