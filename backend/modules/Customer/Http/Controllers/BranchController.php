<?php

namespace Modules\Customer\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Customer\Http\Requests\BranchFormRequest;
use Modules\Customer\Services\BranchService;
use Modules\Customer\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Helpers\EnumHelper;

class BranchController extends Controller implements HasMiddleware
{
    protected BranchService $branchService;
    protected BrandService $brandService;

    public function __construct(BranchService $branchService, BrandService $brandService)
    {
        $this->branchService = $branchService;
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['brand_id', 'search', 'status', 'city', 'state', 'country', 'created_by', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);

            // Check if it's an API request
            if ($request->expectsJson()) {
                $branches = $this->branchService->getAll($filters, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $branches->items(),
                    'current_page' => $branches->currentPage(),
                    'last_page' => $branches->lastPage(),
                    'per_page' => $branches->perPage(),
                    'total' => $branches->total(),
                    'from' => $branches->firstItem(),
                    'to' => $branches->lastItem(),
                    'statistics' => $this->branchService->getBranchesStatistics()
                ]);
            }

            // Return view for web requests
            $title = translate('branches');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('branches')],
            ];

            $actionButtons = [
                [
                    'text' => translate('create') . ' ' . translate('branch'),
                    'class' => 'open-create-modal btn-sm btn-success',
                    'attr' => [
                        'data-modal-link' => route('tenant.branches.create'),
                        'data-modal-title' => translate('create') . ' ' . translate('branch'),
                    ]
                ],
                [
                    'text' => translate('import') . ' ' . translate('branches'),
                    'class' => 'open-create-modal btn-sm btn-info',
                    'attr' => [
                        'data-modal-link' => route('tenant.branches.import'),
                        'data-modal-title' => translate('import') . ' ' . translate('branches'),
                    ]
                ],
            ];

            return view('tenant.customer.branches.index', compact('breadcrumbs', 'title', 'actionButtons'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => translate('message.operation_failed'),
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', translate('something_went_wrong'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = $this->brandService->getByTenant(Auth::user()->tenant_id ?? 1);
        $statusOptions = ['active' => translate('active'), 'inactive' => translate('inactive'), 'suspended' => translate('suspended')];

        return view('tenant.customer.branches.editor', compact('brands', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BranchFormRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Add tenant context
            $data['created_by'] = Auth::user()->id;

            $branch = $this->branchService->create($data);

            return response()->json([
                'success' => true,
                'message' => translate('branch_created_successfully'),
                'data' => $branch
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_create_branch'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $branch = $this->branchService->getById($id);

            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => translate('branch_not_found')
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $branch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_retrieve_branch'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branch = $this->branchService->getById($id);
        if (!$branch) {
            return redirect()->route('tenant.branches.index')->with('error', translate('branch_not_found'));
        }

        $brands = $this->brandService->getByTenant(Auth::user()->tenant_id ?? 1);
        $statusOptions = ['active' => translate('active'), 'inactive' => translate('inactive'), 'suspended' => translate('suspended')];

        return view('tenant.customer.branches.editor', compact('branch', 'brands', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BranchFormRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();

            // Add tenant context
            $data['updated_by'] = Auth::user()->id;

            $updated = $this->branchService->update($id, $data);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => translate('branch_not_found')
                ], 404);
            }

            $branch = $this->branchService->getById($id);

            return response()->json([
                'success' => true,
                'message' => translate('branch_updated_successfully'),
                'data' => $branch
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => translate('validation_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_update_branch'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->branchService->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => translate('branch_not_found')
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => translate('branch_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_delete_branch'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): JsonResponse
    {
        try {
            $restored = $this->branchService->restore($id);

            if (!$restored) {
                return response()->json([
                    'success' => false,
                    'message' => translate('branch_not_found')
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => translate('branch_restored_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_restore_branch'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get branches for a specific brand.
     */
    public function getByBrand(Request $request, string $brandId): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'city', 'state', 'country', 'created_by', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);

            $branches = $this->branchService->getBranchesForBrand($brandId, $filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $branches->items(),
                'pagination' => [
                    'current_page' => $branches->currentPage(),
                    'last_page' => $branches->lastPage(),
                    'per_page' => $branches->perPage(),
                    'total' => $branches->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_retrieve_brand_branches'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search branches.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');

            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => translate('search_query_is_required')
                ], 400);
            }

            $branches = $this->branchService->search($query);

            return response()->json([
                'success' => true,
                'data' => $branches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_search_branches'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->branchService->getBranchesStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_retrieve_branch_statistics'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show import form.
     */
    public function import()
    {
        $title = translate('import_branches');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('branches'), 'link' => route('tenant.branches.index')],
            ['text' => translate('import_branches')],
        ];

        return view('tenant.customer.branches.import', compact('breadcrumbs', 'title'));
    }

    /**
     * Process Excel import.
     */
    public function processImport(Request $request): JsonResponse
    {
        try {
            // Check if it's a file upload or bulk data import
            if ($request->hasFile('file')) {
                $request->validate([
                    'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
                ]);

                $file = $request->file('file');
                $result = $this->branchService->importFromExcel($file);
            } else {
                $request->validate([
                    'import_data' => 'required|array'
                ]);

                $result = $this->branchService->processBulkImport($request->import_data);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('import_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate()
    {
        try {
            $templatePath = $this->branchService->downloadTemplate();

            return response()->download($templatePath, 'branches_template.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', translate('failed_to_download_template'));
        }
    }

    /**
     * Get active branches.
     */
    public function getActiveBranches(): JsonResponse
    {
        try {
            $branches = $this->branchService->getActiveBranches();

            return response()->json([
                'success' => true,
                'data' => $branches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_retrieve_active_branches'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get branches by location.
     */
    public function getByLocation(Request $request): JsonResponse
    {
        try {
            $city = $request->get('city');
            $state = $request->get('state');
            $country = $request->get('country');

            $branches = $this->branchService->getBranchesByLocation($city, $state, $country);

            return response()->json([
                'success' => true,
                'data' => $branches
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_retrieve_branches_by_location'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.branches', only: ['index', 'show', 'getByBrand', 'search', 'stats', 'getActiveBranches', 'getByLocation']),
            new Middleware('permission:create.branches', only: ['create', 'store', 'import', 'processImport', 'downloadTemplate']),
            new Middleware('permission:update.branches', only: ['edit', 'update']),
            new Middleware('permission:delete.branches', only: ['destroy']),
            new Middleware('permission:restore.branches', only: ['restore']),
        ];
    }
}
