<?php

namespace Modules\Expenses\Tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use Modules\Expenses\Application\DTOs\ExpenseData;
use Modules\Expenses\Application\DTOs\ExpenseCategoryData;
use Modules\Expenses\Application\DTOs\ExpenseReportData;
use Modules\Expenses\Application\DTOs\ExpensePolicyData;
use Modules\Expenses\Application\DTOs\ExpenseTagData;
use Modules\Expenses\Application\DTOs\ReimbursementData;

class ExpensesDtoTest extends TestCase
{
    // ── ExpenseData ──────────────────────────────────────────────

    public function test_expense_data_with_data(): void
    {
        $dto = new ExpenseData(data: ['amount' => 100.00, 'description' => 'Taxi ride', 'category_id' => 1]);

        $this->assertSame(['amount' => 100.00, 'description' => 'Taxi ride', 'category_id' => 1], $dto->data);
    }

    public function test_expense_data_to_array_filters_nulls(): void
    {
        $dto = new ExpenseData(data: ['amount' => 50.00, 'description' => null, 'category_id' => 2]);
        $array = $dto->toArray();

        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayHasKey('category_id', $array);
        $this->assertArrayNotHasKey('description', $array);
    }

    public function test_expense_data_default_null(): void
    {
        $dto = new ExpenseData();
        $this->assertNull($dto->data);
    }

    // ── ExpenseCategoryData ──────────────────────────────────────

    public function test_expense_category_data_with_data(): void
    {
        $dto = new ExpenseCategoryData(data: ['name' => 'Travel', 'description' => 'Travel expenses']);

        $this->assertSame(['name' => 'Travel', 'description' => 'Travel expenses'], $dto->data);
    }

    public function test_expense_category_data_to_array_filters_nulls(): void
    {
        $dto = new ExpenseCategoryData(data: ['name' => 'Meals', 'description' => null]);
        $array = $dto->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayNotHasKey('description', $array);
    }

    // ── ExpenseReportData ────────────────────────────────────────

    public function test_expense_report_data_with_data(): void
    {
        $dto = new ExpenseReportData(data: ['title' => 'June Report', 'status' => 'draft']);

        $this->assertSame(['title' => 'June Report', 'status' => 'draft'], $dto->data);
    }

    // ── ExpensePolicyData ─────────────────────────────────────────

    public function test_expense_policy_data_with_data(): void
    {
        $dto = new ExpensePolicyData(data: ['name' => 'Max Amount', 'type' => 'max_amount', 'value' => 500]);

        $this->assertSame(['name' => 'Max Amount', 'type' => 'max_amount', 'value' => 500], $dto->data);
    }

    // ── ExpenseTagData ────────────────────────────────────────────

    public function test_expense_tag_data_with_data(): void
    {
        $dto = new ExpenseTagData(data: ['name' => 'Urgent', 'color' => '#FF0000']);

        $this->assertSame(['name' => 'Urgent', 'color' => '#FF0000'], $dto->data);
    }

    // ── ReimbursementData ────────────────────────────────────────

    public function test_reimbursement_data_with_data(): void
    {
        $dto = new ReimbursementData(data: ['amount' => 250.00, 'status' => 'pending']);

        $this->assertSame(['amount' => 250.00, 'status' => 'pending'], $dto->data);
    }
}
