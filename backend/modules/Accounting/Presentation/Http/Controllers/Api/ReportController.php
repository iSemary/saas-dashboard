<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Accounting\Domain\Strategies\ReportGeneration\ReportGenerationStrategyInterface;

class ReportController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ReportGenerationStrategyInterface $reportStrategy,
    ) {}

    public function generate(Request $request, string $type): JsonResponse
    {
        $allowedTypes = ['trial_balance', 'profit_loss', 'balance_sheet', 'cash_flow'];
        if (!in_array($type, $allowedTypes)) {
            return $this->apiError("Invalid report type. Allowed: " . implode(', ', $allowedTypes), 400);
        }

        $params = $request->only(['from_date', 'to_date', 'fiscal_year_id']);
        $report = $this->reportStrategy->generate($type, $params);
        return $this->apiSuccess($report);
    }
}
