<?php

namespace Modules\Customer\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Customer\Http\Requests\BrandFormRequest;
use Modules\Customer\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BrandWebController extends ApiController implements HasMiddleware
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('can:read.brands', only: ['index', 'show']),
            new Middleware('can:create.brands', only: ['create', 'store']),
            new Middleware('can:update.brands', only: ['edit', 'update']),
            new Middleware('can:delete.brands', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

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
                    'data-modal-link' => route('landlord.brands-web.create'),
                    'data-modal-title' => translate("create") . " " . translate('brand'),
                ]
            ],
        ];

        return view('landlord.customer.brands.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statusOptions = ['active', 'inactive'];
        return view('landlord.customer.brands.editor', compact('statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandFormRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Generate unique slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->brandService->generateUniqueSlug(
                    $data['name'], 
                    $data['tenant_id']
                );
            }

            $brand = $this->brandService->create($data);

            return $this->return(200, translate("created_successfully"));
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $brand = $this->brandService->getById($id);
        
        if (!$brand) {
            abort(404);
        }

        $statusOptions = ['active', 'inactive'];
        return view('landlord.customer.brands.editor', compact('brand', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandFormRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            
            // Generate unique slug if not provided
            if (empty($data['slug'])) {
                $brand = $this->brandService->getById($id);
                if ($brand) {
                    $data['slug'] = $this->brandService->generateUniqueSlug(
                        $data['name'], 
                        $brand->tenant_id,
                        $id
                    );
                }
            }

            $updated = $this->brandService->update($id, $data);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => translate('exception.brand_not_found')
                ], 404);
            }

            return $this->return(200, translate("updated_successfully"));
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = $this->brandService->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => translate('exception.brand_not_found')
                ], 404);
            }

            return $this->return(200, translate("deleted_successfully"));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        try {
            $restored = $this->brandService->restore($id);

            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => translate('exception.brand_not_found')
                ], 404);
            }

            return $this->return(200, translate("restored_successfully"));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
