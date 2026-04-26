<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\UseCases\Expenses\SubmitExpenseClaimUseCase;
use Modules\HR\Infrastructure\Persistence\ExpenseCategoryRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\ExpenseClaimRepositoryInterface;

class ExpenseApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected ExpenseCategoryRepositoryInterface $categoryRepository,
        protected ExpenseClaimRepositoryInterface $claimRepository,
        protected SubmitExpenseClaimUseCase $submitExpenseClaimUseCase,
    ) {
        parent::__construct();
    }

    public function categories(Request $request): JsonResponse
    {
        return $this->success(data: $this->categoryRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $category = $this->categoryRepository->create($request->only(['name', 'max_amount']));
        return $this->success(data: $category, message: translate('message.action_completed'));
    }

    public function claims(Request $request): JsonResponse
    {
        return $this->success(data: $this->claimRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeClaim(Request $request): JsonResponse
    {
        $claim = $this->submitExpenseClaimUseCase->execute(
            $request->only(['employee_id', 'category_id', 'amount', 'currency', 'expense_date', 'description', 'receipt_path'])
        );
        return $this->success(data: $claim, message: translate('message.action_completed'));
    }
}
