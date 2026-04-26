<?php

namespace Modules\Expenses\Tests\Unit;

use Modules\Expenses\Presentation\Http\Requests\StoreExpenseRequest;
use Modules\Expenses\Presentation\Http\Requests\StoreExpenseCategoryRequest;
use Modules\Expenses\Presentation\Http\Requests\StoreExpenseReportRequest;
use Modules\Expenses\Presentation\Http\Requests\StoreExpensePolicyRequest;
use Modules\Expenses\Presentation\Http\Requests\StoreExpenseTagRequest;
use Modules\Expenses\Presentation\Http\Requests\StoreReimbursementRequest;
use Modules\Expenses\Presentation\Http\Requests\UpdateExpenseRequest;
use PHPUnit\Framework\TestCase;

class ExpensesFormRequestTest extends TestCase
{
    public function test_store_expense_has_required_rules(): void
    {
        $request = new StoreExpenseRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('amount', $rules);
        $this->assertArrayHasKey('date', $rules);
        $this->assertArrayHasKey('category_id', $rules);
        $this->assertStringContainsString('required', $rules['title']);
        $this->assertStringContainsString('required', $rules['amount']);
        $this->assertStringContainsString('required', $rules['date']);
        $this->assertStringContainsString('required', $rules['category_id']);
    }

    public function test_store_expense_amount_is_non_negative(): void
    {
        $request = new StoreExpenseRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('min:0', $rules['amount']);
    }

    public function test_store_expense_category_exists_validation(): void
    {
        $request = new StoreExpenseRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('exists:exp_categories,id', $rules['category_id']);
    }

    public function test_update_expense_uses_sometimes_for_partial_updates(): void
    {
        $request = new UpdateExpenseRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('sometimes', $rules['title']);
        $this->assertStringContainsString('sometimes', $rules['amount']);
    }

    public function test_store_expense_category_has_required_name(): void
    {
        $request = new StoreExpenseCategoryRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertStringContainsString('required', $rules['name']);
    }

    public function test_store_expense_category_max_amount_is_non_negative(): void
    {
        $request = new StoreExpenseCategoryRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('min:0', $rules['max_amount']);
    }

    public function test_store_expense_report_has_required_title(): void
    {
        $request = new StoreExpenseReportRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('title', $rules);
        $this->assertStringContainsString('required', $rules['title']);
    }

    public function test_store_expense_policy_type_is_enum_validated(): void
    {
        $request = new StoreExpensePolicyRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('in:max_amount,receipt_required,approval_required,category_restriction', $rules['type']);
    }

    public function test_store_expense_tag_has_required_name(): void
    {
        $request = new StoreExpenseTagRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertStringContainsString('required', $rules['name']);
    }

    public function test_store_reimbursement_has_required_amount(): void
    {
        $request = new StoreReimbursementRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('amount', $rules);
        $this->assertStringContainsString('required', $rules['amount']);
        $this->assertStringContainsString('min:0', $rules['amount']);
    }
}
