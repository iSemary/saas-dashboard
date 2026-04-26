<?php

namespace Modules\Sales\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\Sales\Domain\Strategies\Payment\CashPaymentStrategy;
use Modules\Sales\Domain\Strategies\Payment\CardPaymentStrategy;
use Modules\Sales\Domain\Strategies\Payment\InstallmentPaymentStrategy;
use Modules\Sales\Domain\Strategies\OrderType\TakeawayOrderStrategy;
use Modules\Sales\Domain\Strategies\OrderType\DineInOrderStrategy;
use Modules\Sales\Domain\Strategies\OrderType\DeliveryOrderStrategy;

class SalesStrategyTest extends TestCase
{
    // ── CashPaymentStrategy ───────────────────────────────────────

    public function test_cash_get_method(): void
    {
        $this->assertSame('cash', (new CashPaymentStrategy())->getMethod());
    }

    public function test_cash_validate_passes_with_amount_paid(): void
    {
        $strategy = new CashPaymentStrategy();
        $strategy->validate(['amount_paid' => 100]);
        $this->assertTrue(true); // no exception thrown
    }

    public function test_cash_validate_fails_without_amount_paid(): void
    {
        $this->expectException(\DomainException::class);
        (new CashPaymentStrategy())->validate([]);
    }

    public function test_cash_process_sets_method_and_null_transaction(): void
    {
        $result = (new CashPaymentStrategy())->process(['total_price' => 200, 'amount_paid' => 200]);
        $this->assertSame('cash', $result['pay_method']);
        $this->assertNull($result['transaction_number']);
    }

    // ── CardPaymentStrategy ──────────────────────────────────────

    public function test_card_get_method(): void
    {
        $this->assertSame('card', (new CardPaymentStrategy())->getMethod());
    }

    public function test_card_validate_passes_with_transaction_number(): void
    {
        $strategy = new CardPaymentStrategy();
        $strategy->validate(['transaction_number' => 'TXN-123']);
        $this->assertTrue(true);
    }

    public function test_card_validate_fails_without_transaction_number(): void
    {
        $this->expectException(\DomainException::class);
        (new CardPaymentStrategy())->validate([]);
    }

    public function test_card_process_sets_amount_paid_to_total_price(): void
    {
        $result = (new CardPaymentStrategy())->process([
            'total_price' => 250,
            'transaction_number' => 'TXN-456',
        ]);
        $this->assertSame('card', $result['pay_method']);
        $this->assertSame(250, $result['amount_paid']);
    }

    // ── InstallmentPaymentStrategy ───────────────────────────────

    public function test_installment_get_method(): void
    {
        $this->assertSame('installment', (new InstallmentPaymentStrategy())->getMethod());
    }

    public function test_installment_validate_passes_with_valid_data(): void
    {
        $strategy = new InstallmentPaymentStrategy();
        $strategy->validate(['total_months' => 6, 'monthly_amount' => 500]);
        $this->assertTrue(true);
    }

    public function test_installment_validate_fails_without_total_months(): void
    {
        $this->expectException(\DomainException::class);
        (new InstallmentPaymentStrategy())->validate(['monthly_amount' => 500]);
    }

    public function test_installment_validate_fails_without_monthly_amount(): void
    {
        $this->expectException(\DomainException::class);
        (new InstallmentPaymentStrategy())->validate(['total_months' => 6]);
    }

    public function test_installment_validate_fails_with_zero_monthly_amount(): void
    {
        $this->expectException(\DomainException::class);
        (new InstallmentPaymentStrategy())->validate(['total_months' => 6, 'monthly_amount' => 0]);
    }

    public function test_installment_process_sets_method(): void
    {
        $result = (new InstallmentPaymentStrategy())->process([
            'total_months' => 6,
            'monthly_amount' => 500,
        ]);
        $this->assertSame('installment', $result['pay_method']);
    }

    // ── TakeawayOrderStrategy ─────────────────────────────────────

    public function test_takeaway_get_type(): void
    {
        $this->assertSame('takeaway', (new TakeawayOrderStrategy())->getType());
    }

    public function test_takeaway_prepare_sets_order_type(): void
    {
        $result = (new TakeawayOrderStrategy())->prepare(['client_id' => 1]);
        $this->assertSame('takeaway', $result['order_type']);
        $this->assertSame(1, $result['client_id']);
    }

    // ── DineInOrderStrategy ───────────────────────────────────────

    public function test_dine_in_get_type(): void
    {
        $this->assertSame('dine_in', (new DineInOrderStrategy())->getType());
    }

    public function test_dine_in_prepare_sets_order_type(): void
    {
        $result = (new DineInOrderStrategy())->prepare(['client_id' => 1]);
        $this->assertSame('dine_in', $result['order_type']);
    }

    // ── DeliveryOrderStrategy ────────────────────────────────────

    public function test_delivery_get_type(): void
    {
        $this->assertSame('delivery', (new DeliveryOrderStrategy())->getType());
    }

    public function test_delivery_prepare_sets_order_type(): void
    {
        $result = (new DeliveryOrderStrategy())->prepare(['client_id' => 1]);
        $this->assertSame('delivery', $result['order_type']);
    }
}
