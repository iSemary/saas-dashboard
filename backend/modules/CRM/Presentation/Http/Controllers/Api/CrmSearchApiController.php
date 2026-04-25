<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;
use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;
use Modules\CRM\Repositories\ContactRepositoryInterface;
use Modules\CRM\Repositories\CompanyRepositoryInterface;

class CrmSearchApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        private readonly LeadRepositoryInterface $leads,
        private readonly OpportunityRepositoryInterface $opportunities,
        private readonly ContactRepositoryInterface $contacts,
        private readonly CompanyRepositoryInterface $companies,
    ) {}

    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate(['q' => 'required|string|min:2|max:255']);
            $query = $request->input('q');
            $limit = (int) $request->get('limit', 5);

            $results = [
                'leads' => $this->leads->search($query)->take($limit)->values(),
                'opportunities' => $this->opportunities->paginate(['search' => $query], $limit)->items(),
                'contacts' => $this->contacts->paginate(['search' => $query], $limit)->items(),
                'companies' => $this->companies->paginate(['search' => $query], $limit)->items(),
            ];

            $total = collect($results)->sum(fn($items) => count($items));

            return $this->apiSuccess([
                'query' => $query,
                'total' => $total,
                'results' => $results,
            ]);
        } catch (\Throwable $e) {
            return $this->apiError('Search failed', 500, $e->getMessage());
        }
    }
}
