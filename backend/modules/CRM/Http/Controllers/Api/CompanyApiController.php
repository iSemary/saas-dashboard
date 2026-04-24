<?php

namespace Modules\CRM\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\CRM\Models\Company;
use Modules\CRM\Services\CompanyService;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;

class CompanyApiController extends ApiController
{
    protected $service;

    public function __construct(CompanyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['type', 'industry', 'search', 'assigned_to', 'per_page']);
            $perPage = $request->get('per_page', 15);
            
            $query = Company::with(['assignedUser', 'creator', 'contacts']);

            // Filter by type (default to 'customer' for customer management)
            if ($request->has('type')) {
                $query->where('type', $request->type);
            } else {
                $query->where('type', 'customer');
            }

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filter by industry
            if ($request->has('industry') && $request->industry) {
                $query->where('industry', $request->industry);
            }

            // Filter by assigned user
            if ($request->has('assigned_to') && $request->assigned_to) {
                $query->where('assigned_to', $request->assigned_to);
            }

            $companies = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'data' => [
                    'data' => $companies->items(),
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                    'from' => $companies->firstItem(),
                    'to' => $companies->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve companies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'website' => 'nullable|url|max:255',
                'industry' => 'nullable|string|max:255',
                'employee_count' => 'nullable|integer|min:0',
                'annual_revenue' => 'nullable|numeric|min:0',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
                'type' => 'nullable|in:customer,prospect,partner,vendor,competitor',
                'assigned_to' => 'nullable|exists:users,id',
                'custom_fields' => 'nullable|array',
            ]);

            $validated['created_by'] = auth()->id();
            $validated['type'] = $validated['type'] ?? 'customer';

            $company = Company::create($validated);

            return response()->json([
                'data' => $company->load(['assignedUser', 'creator']),
                'message' => 'Company created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $company = Company::with(['assignedUser', 'creator', 'contacts', 'opportunities'])->findOrFail($id);
            return response()->json(['data' => $company]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Company not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'website' => 'nullable|url|max:255',
                'industry' => 'nullable|string|max:255',
                'employee_count' => 'nullable|integer|min:0',
                'annual_revenue' => 'nullable|numeric|min:0',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
                'type' => 'nullable|in:customer,prospect,partner,vendor,competitor',
                'assigned_to' => 'nullable|exists:users,id',
                'custom_fields' => 'nullable|array',
            ]);

            $company->update($validated);

            return response()->json([
                'data' => $company->load(['assignedUser', 'creator', 'contacts']),
                'message' => 'Company updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();

            return response()->json([
                'message' => 'Company deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function activity($id)
    {
        try {
            $company = Company::findOrFail($id);
            
            $audits = Audit::where('auditable_type', Company::class)
                ->where('auditable_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'data' => [
                    'data' => $audits->items(),
                    'current_page' => $audits->currentPage(),
                    'last_page' => $audits->lastPage(),
                    'per_page' => $audits->perPage(),
                    'total' => $audits->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:companies,id'
            ]);

            $deleted = Company::whereIn('id', $request->ids)->delete();

            return response()->json([
                'message' => "{$deleted} companies deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete companies',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
