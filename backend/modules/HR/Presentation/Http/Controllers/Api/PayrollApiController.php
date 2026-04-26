<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\DTOs\GeneratePayrollData;
use Modules\HR\Application\UseCases\Payroll\GeneratePayrollUseCase;
use Modules\HR\Application\UseCases\Payroll\CalculatePayrollUseCase;
use Modules\HR\Application\UseCases\Payroll\ApprovePayrollUseCase;
use Modules\HR\Application\UseCases\Payroll\MarkPaidUseCase;
use Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface;

class PayrollApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected PayrollRepositoryInterface $repository,
        protected GeneratePayrollUseCase $generatePayrollUseCase,
        protected CalculatePayrollUseCase $calculatePayrollUseCase,
        protected ApprovePayrollUseCase $approvePayrollUseCase,
        protected MarkPaidUseCase $markPaidUseCase,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $payrolls = $this->repository->paginate(
            filters: $request->only(['search', 'employee_id', 'status', 'pay_period_start', 'pay_period_end']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $payrolls);
    }

    public function show(int $id): JsonResponse
    {
        $payroll = $this->repository->findOrFail($id);
        return $this->success(data: $payroll);
    }

    public function generate(Request $request): JsonResponse
    {
        $data = new GeneratePayrollData(
            employeeId: $request->input('employee_id'),
            payPeriodStart: $request->input('pay_period_start'),
            payPeriodEnd: $request->input('pay_period_end'),
            payDate: $request->input('pay_date'),
            notes: $request->input('notes'),
        );
        
        $payroll = $this->generatePayrollUseCase->execute($data);
        return $this->success(data: $payroll, message: translate('message.action_completed'));
    }

    public function calculate(int $id): JsonResponse
    {
        $payroll = $this->calculatePayrollUseCase->execute($id);
        return $this->success(data: $payroll, message: translate('message.action_completed'));
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $payroll = $this->approvePayrollUseCase->execute($id, $request->input('notes'));
        return $this->success(data: $payroll, message: translate('message.action_completed'));
    }

    public function markPaid(int $id): JsonResponse
    {
        $payroll = $this->markPaidUseCase->execute($id);
        return $this->success(data: $payroll, message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function byEmployee(int $employeeId): JsonResponse
    {
        $payrolls = $this->repository->paginate(
            filters: ['employee_id' => $employeeId],
            perPage: 100
        );
        return $this->success(data: $payrolls);
    }

    public function byStatus(Request $request, string $status): JsonResponse
    {
        $payrolls = $this->repository->getByStatus($status);
        return $this->success(data: $payrolls);
    }
}
