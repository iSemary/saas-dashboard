<?php

namespace Modules\Customer\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Customer\Repository\Tenant\BrandRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BrandController extends Controller implements HasMiddleware
{
    protected BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('can:read.brands', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Check if it's an AJAX/DataTables request
            if ($request->ajax()) {
                return $this->brandRepository->datatables();
            }

            $filters = $request->only(['search', 'status', 'created_by', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 15);

            // Check if it's an API request
            if ($request->expectsJson()) {
                $brands = $this->brandRepository->getAll($filters, $perPage);

                return response()->json([
                    'success' => true,
                    'data' => $brands,
                    'statistics' => $this->brandRepository->getDashboardStats()
                ]);
            }

            // Return view for web requests
            $title = translate('brands');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('brands')],
            ];

            return view('tenant.customer.brands.index', compact('breadcrumbs', 'title'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve brands.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', translate('something_went_wrong'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        try {
            $brand = $this->brandRepository->getById($id);
            
            if (!$brand) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Brand not found.'
                    ], 404);
                }
                
                return redirect()->back()->with('error', translate('brand_not_found'));
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $brand
                ]);
            }

            $title = translate('brand_details');
            $breadcrumbs = [
                ['text' => translate('home'), 'link' => route('home')],
                ['text' => translate('brands'), 'link' => route('tenant.brands.index')],
                ['text' => $brand->name],
            ];

            return view('tenant.customer.brands.show', compact('brand', 'breadcrumbs', 'title'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve brand.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', translate('something_went_wrong'));
        }
    }

    /**
     * Get brands statistics for dashboard.
     */
    public function stats(Request $request)
    {
        try {
            $stats = $this->brandRepository->getDashboardStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brand statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search brands.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $brands = $this->brandRepository->search($query);
            
            return response()->json([
                'success' => true,
                'data' => $brands
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search brands.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
