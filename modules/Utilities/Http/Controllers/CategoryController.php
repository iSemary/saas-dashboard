<?php

namespace Modules\Utilities\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Utilities\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = $this->service->model->pluralTitle;
        $breadcrumbs = [
            ['text' => 'Home', 'link' => route('home')],
            ['text' => $this->service->model->pluralTitle],
        ];

        $actionButtons = [
            [
                'text' => 'Add ' . $this->service->model->singleTitle,
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.categories.create'),
                    'data-modal-title' => "Create " . $this->service->model->singleTitle,
                ]
            ],
        ];

        return view('landlord.geography.categories.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        return view('landlord.geography.categories.editor');
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
        return view('landlord.geography.categories.editor', compact('row'));
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
