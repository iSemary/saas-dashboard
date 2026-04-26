<?php

namespace Modules\CRM\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\CRM\DTOs\CreateCompanyData;
use Modules\CRM\DTOs\UpdateCompanyData;
use Modules\CRM\Http\Requests\StoreCompanyRequest;
use Modules\CRM\Http\Requests\UpdateCompanyRequest;
use Modules\CRM\Services\CompanyService;

class CompanyApiController extends ApiController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.crm.companies', only: ['index', 'show', 'activity']),
            new Middleware('permission:create.crm.companies', only: ['store']),
            new Middleware('permission:update.crm.companies', only: ['update']),
            new Middleware('permission:delete.crm.companies', only: ['destroy', 'bulkDelete']),
        ];
    }

    public function __construct(protected CompanyService $service) {}

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['type', 'industry', 'search', 'assigned_to']);
            $perPage = $request->get('per_page', 15);

            $companies = $this->service->list($filters, $perPage);

            return $this->return(200, '', $companies);
        } catch (\Exception $e) {
            return $this->return(500, 'Failed to retrieve companies', [], ['error' => $e->getMessage()]);
        }
    }

    public function store(StoreCompanyRequest $request)
    {
        try {
            $data = CreateCompanyData::fromRequest($request);
            $company = $this->service->create($data);

            return $this->respondCreated($company->load(['assignedUser', 'creator'])->toArray());
        } catch (\Exception $e) {
            return $this->return(500, 'Failed to create company', [], ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $company = $this->service->findOrFail($id);
            return $this->respondWithArray($company->toArray());
        } catch (\Exception $e) {
            return $this->respondNotFound('Company not found');
        }
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            $data = UpdateCompanyData::fromRequest($request);
            $company = $this->service->update($id, $data);

            return $this->respondWithArray($company->toArray(), 'Company updated successfully');
        } catch (\Exception $e) {
            return $this->return(500, 'Failed to update company', [], ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);

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
            $audits = $this->service->getActivity($id);

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

            $deleted = $this->service->bulkDelete($request->ids);

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
