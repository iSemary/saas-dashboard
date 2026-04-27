<?php

namespace Modules\FileManager\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\FileManager\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class FileController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(FileService $service)
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

        // $actionButtons = [
        //     [
        //         'text' => translate("create") . " " . translate($this->service->model->singleTitle),
        //         'class' => 'open-create-modal btn-sm btn-success',
        //         'attr' => [
        //             'data-modal-link' => route('landlord.development.files.create'),
        //             'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
        //         ]
        //     ],
        // ];

        return view('landlord.file-managers.files.index', compact('breadcrumbs', 'title'));
    }

    public function create()
    {
        return view('landlord.file-managers.files.editor');
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
        return view('landlord.file-managers.files.editor', compact('row'));
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

    public function manager()
    {
        return view('landlord.file-managers.files.manager');
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.files', only: ['index', 'show', 'manager']),
            new Middleware('permission:create.files', only: ['create', 'store']),
            new Middleware('permission:update.files', only: ['edit', 'update']),
            new Middleware('permission:delete.files', only: ['destroy']),
            new Middleware('permission:restore.files', only: ['restore']),
        ];
    }
}
