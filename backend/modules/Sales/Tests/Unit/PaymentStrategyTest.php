<?php

namespace Modules\Sales\Tests\Unit;

use Modules\Sales\Domain\Strategies\Payment\CardPaymentStrategy;
use Modules\Sales\Domain\Strategies\Payment\CashPaymentStrategy;
use Modules\Sales\Domain\Strategies\Payment\InstallmentPaymentStrategy;
use PHPUnit\Framework\TestCase;

class PaymentStrategyTest extends TestCase
{
    public function test_cash_strategy_returns_cash_method(): void
    {
        $strategy = new CashPaymentStrategy();
        $this->assertSame('cash', $strategy->getMethod());
    }

    public function test_cash_strategy_validates_with_amount_paid(): void
    {
        $strategy = new CashPaymentStrategy();
        // Should not throw
        $strategy->validate(['total_price' => 100.0, 'amount_paid' => 100.0]);
        $this->assertTrue(true);
    }

    public function test_cash_strategy_throws_without_amount_paid(): void
    {
        $strategy = new CashPaymentStrategy();
        $this->expectException(\DomainException::class);
        $strategy->validate(['total_price' => 100.0]);
    }

    public function test_cash_strategy_process_sets_pay_method(): void
    {
        $strategy = new CashPaymentStrategy();
        $result = $strategy->process(['amount_paid' => 100.0]);
        $this->assertSame('cash', $result['pay_method']);
        $this->assertNull($result['transaction_number']);
    }

    public function test_card_strategy_returns_card_method(): void
    {
        $strategy = new CardPaymentStrategy();
        $this->assertSame('card', $strategy->getMethod());
    }

    public function test_card_strategy_requires_transaction_number(): void
    {
        $strategy = new CardPaymentStrategy();
        $this->expectException(\DomainException::class);
        $strategy->validate(['total_price' => 100.0]);
    }

    public function test_card_strategy_passes_with_transaction_number(): void
    {
        $strategy = new CardPaymentStrategy();
        // Should not throw
        $strategy->validate(['total_price' => 100.0, 'transaction_number' => 'TXN-001']);
        $this->assertTrue(true);
    }

    public function test_card_strategy_process_sets_amount_paid(): void
    {
        $strategy = new CardPaymentStrategy();
        $result = $strategy->process(['total_price' => 100.0, 'transaction_number' => 'TXN-001']);
        $this->assertSame('card', $result['pay_method']);
        $this->assertEquals(100.0, $result['amount_paid']);
    }

    public function test_installment_strategy_returns_installment_method(): void
    {
        $strategy = new InstallmentPaymentStrategy();
        $this->assertSame('installment', $strategy->getMethod());
    }

    public function test_installment_strategy_requires_months_and_amount(): void
    {
        $strategy = new InstallmentPaymentStrategy();
        $this->expectException(\DomainException::class);
        $strategy->validate(['total_price' => 100.0]);
    }

    public function test_installment_strategy_passes_with_required_fields(): void
    {
        $strategy = new InstallmentPaymentStrategy();
        // Should not throw
        $strategy->validate([
            'total_price'    => 100.0,
            'total_months'   => 5,
            'monthly_amount' => 20.0,
        ]);
        $this->assertTrue(true);
    }
}
