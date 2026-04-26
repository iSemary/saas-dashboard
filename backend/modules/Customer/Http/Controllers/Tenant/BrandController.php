<?php

namespace Modules\Customer\Http\Controllers\Tenant;

use App\Http\Controllers\ApiController;
use Modules\Customer\Services\Tenant\BrandService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BrandController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.brands', only: ['index', 'show']),
            new Middleware('permission:create.brands', only: ['create', 'store']),
            new Middleware('permission:update.brands', only: ['edit', 'update']),
            new Middleware('permission:delete.brands', only: ['destroy']),
            new Middleware('permission:restore.brands', only: ['restore']),
        ];
    }

    /**
     * Display a listing of brands
     */
    public function index()
    {
        if (request()->ajax())
        {
            return $this->service->getDataTables();
        }

        $title = translate('brands');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('brands')],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate('brand'),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('tenant.brands.create'),
                    'data-modal-title' => translate("create") . " " . translate('brand'),
                ]
            ],
        ];

        return view('tenant.customer.brands.index', compact('title', 'breadcrumbs', 'actionButtons'));
    }

    /**
     * Show the form for creating a new brand
     */
    public function create()
    {
        return view('tenant.customer.brands.editor');
    }

    /**
     * Store a newly created brand
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return $this->return(200, translate("created_successfully"));
    }

    /**
     * Display the specified brand
     */
    public function show($id)
    {
        $brand = $this->service->get($id);
        if (!$brand) {
            return $this->return(404, translate('brand_not_found'));
        }
        return $this->return(200, translate('brand_retrieved_successfully'), $brand);
    }

    /**
     * Show the form for editing the specified brand
     */
    public function edit($id)
    {
        $row = $this->service->get($id);
        return view('tenant.customer.brands.editor', compact('row'));
    }

    /**
     * Update the specified brand
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return $this->return(200, translate("updated_successfully"));
    }

    /**
     * Remove the specified brand
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, translate("deleted_successfully"));
    }

    /**
     * Restore the specified brand
     */
    public function restore($id)
    {
        $this->service->restore($id);
        return $this->return(200, translate("restored_successfully"));
    }


    /**
     * Get modules for a specific brand (AJAX endpoint)
     */
    public function getModules(int $id)
    {
        try
        {
            $modules = $this->service->getBrandModules($id);

            return $this->return(200, translate('modules_retrieved_successfully'), $modules);
        }
        catch (\Exception $e)
        {
            return $this->return(500, translate('something_went_wrong'), debug: $e->getMessage());
        }
    }

    /**
     * Get brands for dashboard (AJAX endpoint)
     */
    public function getBrandsForDashboard()
    {
        try
        {
            $brands = $this->service->getBrandsForDashboard();

            return $this->return(200, translate('brands_retrieved_successfully'), $brands->toArray());
        }
        catch (\Exception $e)
        {
            return $this->return(500, translate('something_went_wrong'), debug: $e->getMessage());
        }
    }

    /**
     * Assign modules to a brand
     */
    public function assignModules(Request $request, int $id)
    {
        try
        {
            $request->validate([
                'module_ids' => 'required|array',
                'module_ids.*' => 'exists:modules,id'
            ]);

            $this->service->assignModules($id, $request->module_ids);

            return $this->return(200, translate('modules_assigned_successfully'));
        }
        catch (\Exception $e)
        {
            return $this->return(500, translate('something_went_wrong'), debug: $e->getMessage());
        }
    }
}
