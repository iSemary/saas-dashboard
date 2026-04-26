<?php

namespace Modules\Expenses\Tests\Unit;

use Modules\Expenses\Application\UseCases\SubmitExpense;
use Modules\Expenses\Domain\Entities\Expense;
use Modules\Expenses\Domain\Strategies\PolicyValidation\PolicyValidationStrategyInterface;
use Modules\Expenses\Domain\Strategies\ExpenseApproval\ExpenseApprovalStrategyInterface;
use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use PHPUnit\Framework\TestCase;

class SubmitExpenseUseCaseTest extends TestCase
{
    public function test_submit_calls_submit_on_expense_when_not_auto_approved(): void
    {
        $expense = $this->createMock(Expense::class);
        $expense->method('submit')->willReturnSelf();
        $expense->method('fresh')->willReturnSelf();

        $repo = $this->createMock(ExpenseRepositoryInterface::class);
        $repo->method('findOrFail')->willReturn($expense);

        $policy = $this->createMock(PolicyValidationStrategyInterface::class);
        $policy->method('validate')->willReturn(true);

        $approval = $this->createMock(ExpenseApprovalStrategyInterface::class);
        $approval->method('shouldAutoApprove')->willReturn(false);

        $expense->expects($this->once())->method('submit');

        $useCase = new SubmitExpense($repo, $policy, $approval);
        $result = $useCase->execute(1);

        $this->assertInstanceOf(Expense::class, $result);
    }

    public function test_submit_auto_approves_when_strategy_says_so(): void
    {
        $expense = $this->createMock(Expense::class);
        $expense->method('fresh')->willReturnSelf();

        $repo = $this->createMock(ExpenseRepositoryInterface::class);
        $repo->method('findOrFail')->willReturn($expense);

        $policy = $this->createMock(PolicyValidationStrategyInterface::class);
        $policy->method('validate')->willReturn(true);

        $approval = $this->createMock(ExpenseApprovalStrategyInterface::class);
        $approval->method('shouldAutoApprove')->willReturn(true);

        // When auto-approved, transitionState + approve are called, NOT submit
        $expense->expects($this->once())->method('transitionState');
        $expense->expects($this->once())->method('approve');
        $expense->expects($this->never())->method('submit');

        $useCase = new SubmitExpense($repo, $policy, $approval);
        $result = $useCase->execute(1);

        $this->assertInstanceOf(Expense::class, $result);
    }
}
