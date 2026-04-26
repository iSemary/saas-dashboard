<?php

namespace Modules\Sales\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\Sales\Domain\Enums\OrderStatus;
use Modules\Sales\Domain\Enums\PaymentMethod;
use Modules\Sales\Domain\Enums\SalesOrderType;

class SalesValueObjectTest extends TestCase
{
    // ── OrderStatus ──────────────────────────────────────────────

    public function test_order_status_values(): void
    {
        $this->assertSame('pending', OrderStatus::Pending->value);
        $this->assertSame('completed', OrderStatus::Completed->value);
        $this->assertSame('cancelled', OrderStatus::Cancelled->value);
        $this->assertSame('returned', OrderStatus::Returned->value);
    }

    public function test_order_status_all_cases_covered(): void
    {
        $this->assertCount(4, OrderStatus::cases());
    }

    // ── PaymentMethod ─────────────────────────────────────────────

    public function test_payment_method_values(): void
    {
        $this->assertSame('cash', PaymentMethod::Cash->value);
        $this->assertSame('card', PaymentMethod::Card->value);
        $this->assertSame('installment', PaymentMethod::Installment->value);
        $this->assertSame('transfer', PaymentMethod::Transfer->value);
    }

    public function test_payment_method_all_cases_covered(): void
    {
        $this->assertCount(4, PaymentMethod::cases());
    }

    // ── SalesOrderType ───────────────────────────────────────────

    public function test_sales_order_type_values(): void
    {
        $this->assertSame('takeaway', SalesOrderType::Takeaway->value);
        $this->assertSame('dine_in', SalesOrderType::DineIn->value);
        $this->assertSame('delivery', SalesOrderType::Delivery->value);
        $this->assertSame('steward', SalesOrderType::Steward->value);
    }

    public function test_sales_order_type_all_cases_covered(): void
    {
        $this->assertCount(4, SalesOrderType::cases());
    }
}
