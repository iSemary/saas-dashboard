<?php

namespace Modules\HR\Tests\Unit;

use Modules\HR\Application\UseCases\Expenses\SubmitExpenseClaimUseCase;
use Modules\HR\Domain\Entities\ExpenseClaim;
use Modules\HR\Infrastructure\Persistence\ExpenseClaimRepositoryInterface;
use PHPUnit\Framework\TestCase;

class SubmitExpenseClaimUseCaseTest extends TestCase
{
    public function test_sets_default_status_when_missing(): void
    {
        $repo = $this->createMock(ExpenseClaimRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('create')
            ->with($this->callback(function (array $data) {
                return ($data['status'] ?? null) === 'submitted';
            }))
            ->willReturn(new ExpenseClaim());

        $useCase = new SubmitExpenseClaimUseCase($repo);
        $result = $useCase->execute([
            'employee_id' => 1,
            'amount' => 100,
            'currency' => 'USD',
        ]);

        $this->assertInstanceOf(ExpenseClaim::class, $result);
    }
}
