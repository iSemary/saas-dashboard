<?php

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\FileManager\Services\FileService;
use Illuminate\Http\Request;

class NotificationController extends ApiController
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
        return $this->return(200, "Created successfully");
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
        return $this->return(200, "Updated successfully");
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, "Deleted successfully");
    }
}
