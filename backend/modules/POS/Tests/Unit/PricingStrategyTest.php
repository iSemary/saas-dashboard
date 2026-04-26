<?php

namespace Modules\POS\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\POS\Domain\Strategies\Pricing\RegularPricingStrategy;
use Modules\POS\Domain\Strategies\Pricing\OfferPricingStrategy;
use Modules\POS\Domain\Strategies\Pricing\WholesalePricingStrategy;

class PricingStrategyTest extends TestCase
{
    // ── RegularPricingStrategy ────────────────────────────────────

    public function test_regular_calculates_base_price_times_quantity(): void
    {
        $strategy = new RegularPricingStrategy();
        $result = $strategy->calculate(10.0, 3);

        $this->assertSame(30.0, $result->amount);
    }

    public function test_regular_supports_regular_type(): void
    {
        $strategy = new RegularPricingStrategy();
        $this->assertTrue($strategy->supports('regular'));
        $this->assertFalse($strategy->supports('offer'));
        $this->assertFalse($strategy->supports('wholesale'));
    }

    public function test_regular_rounds_to_two_decimals(): void
    {
        $strategy = new RegularPricingStrategy();
        $result = $strategy->calculate(9.99, 3);

        $this->assertSame(29.97, $result->amount);
    }

    // ── OfferPricingStrategy ─────────────────────────────────────

    public function test_offer_applies_percentage_discount(): void
    {
        $strategy = new OfferPricingStrategy();
        $result = $strategy->calculate(100.0, 2, ['offer_percentage' => 25]);

        // 100 * (1 - 25/100) = 75 per unit, * 2 = 150
        $this->assertSame(150.0, $result->amount);
    }

    public function test_offer_zero_percentage_equals_regular(): void
    {
        $strategy = new OfferPricingStrategy();
        $result = $strategy->calculate(50.0, 3, ['offer_percentage' => 0]);

        $this->assertSame(150.0, $result->amount);
    }

    public function test_offer_supports_offer_type(): void
    {
        $strategy = new OfferPricingStrategy();
        $this->assertTrue($strategy->supports('offer'));
        $this->assertFalse($strategy->supports('regular'));
    }

    public function test_offer_missing_context_defaults_to_zero_discount(): void
    {
        $strategy = new OfferPricingStrategy();
        $result = $strategy->calculate(100.0, 1);

        $this->assertSame(100.0, $result->amount);
    }

    // ── WholesalePricingStrategy ──────────────────────────────────

    public function test_wholesale_uses_wholesale_price_from_context(): void
    {
        $strategy = new WholesalePricingStrategy();
        $result = $strategy->calculate(100.0, 5, ['wholesale_price' => 80.0]);

        $this->assertSame(400.0, $result->amount);
    }

    public function test_wholesale_falls_back_to_base_price(): void
    {
        $strategy = new WholesalePricingStrategy();
        $result = $strategy->calculate(100.0, 2);

        $this->assertSame(200.0, $result->amount);
    }

    public function test_wholesale_supports_wholesale_type(): void
    {
        $strategy = new WholesalePricingStrategy();
        $this->assertTrue($strategy->supports('wholesale'));
        $this->assertFalse($strategy->supports('regular'));
    }
}
