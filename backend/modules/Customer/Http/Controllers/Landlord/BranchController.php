<?php

namespace Modules\Customer\Http\Controllers\Landlord;

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

    public static function middleware(): array
    {
        return [
            new Middleware('can:read.branches', only: ['index', 'show', 'getByBrand', 'getByLocation', 'search', 'stats', 'import', 'downloadTemplate']),
            new Middleware('can:create.branches', only: ['create', 'store', 'processImport']),
            new Middleware('can:update.branches', only: ['edit', 'update']),
            new Middleware('can:delete.branches', only: ['destroy']),
            new Middleware('can:restore.branches', only: ['restore']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->branchService->getDataTables();
        }

        $title = translate('branches');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('branches')],
        ];

        $actionButtons = [
            [
                'text' => translate('create') . ' ' . translate('branch'),
                'class' => 'open-create-modal btn-sm btn-primary',
                'attr' => [
                    'data-modal-link' => route('landlord.branches.create'),
                    'data-modal-title' => translate('create') . ' ' . translate('branch'),
                ]
            ],
            [
                'text' => translate('import') . ' ' . translate('branches'),
                'class' => 'open-create-modal btn-sm btn-info',
                'attr' => [
                    'data-modal-link' => route('landlord.branches.import'),
                    'data-modal-title' => translate('import') . ' ' . translate('branches'),
                ]
            ],
        ];

        return view('landlord.customer.branches.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = $this->brandService->getAll(); // Get all brands for landlord
        $statusOptions = EnumHelper::getEnumFromTable('branches', 'status');
        return view('landlord.customer.branches.editor', compact('brands', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BranchFormRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Add landlord context
            $data['created_by'] = Auth::user()->id;

            $branch = $this->branchService->create($data);

            return response()->json([
                'success' => true,
                'message' => translate('created_successfully'),
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
                'message' => translate('failed_to_create_branch') . ': ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $row = $this->branchService->getById($id);
        if (!$row) {
            abort(404);
        }
        $brands = $this->brandService->getAll(); // Get all brands for landlord
        $statusOptions = EnumHelper::getEnumFromTable('branches', 'status');
        return view('landlord.customer.branches.editor', compact('row', 'brands', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BranchFormRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();

            // Add landlord context
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
                'message' => translate('updated_successfully'),
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
                'message' => translate('failed_to_update_branch') . ': ' . $e->getMessage(),
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
                'message' => translate('deleted_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_delete_branch') . ': ' . $e->getMessage(),
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
                'message' => translate('restored_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_restore_branch') . ': ' . $e->getMessage(),
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
            $filters = $request->only(['search', 'status', 'created_by', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);

            $branches = $this->branchService->getAll(array_merge($filters, ['brand_id' => $brandId]), $perPage);

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
                'message' => translate('failed_to_retrieve_brand_branches') . ': ' . $e->getMessage(),
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
                'message' => translate('failed_to_retrieve_branches_by_location') . ': ' . $e->getMessage(),
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
                'message' => translate('failed_to_search_branches') . ': ' . $e->getMessage(),
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
            $stats = $this->branchService->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_retrieve_branch_statistics') . ': ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for importing branches.
     */
    public function import()
    {
        $title = translate('import_branches');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('branches'), 'link' => route('landlord.branches.index')],
            ['text' => translate('import_branches')],
        ];

        return view('landlord.customer.branches.import', compact('breadcrumbs', 'title'));
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
            $filePath = $this->branchService->downloadTemplate();
            return response()->download(public_path($filePath));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('failed_to_download_template') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}

