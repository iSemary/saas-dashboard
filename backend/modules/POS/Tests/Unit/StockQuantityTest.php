<?php

namespace Modules\POS\Tests\Unit;

use Modules\POS\Domain\ValueObjects\StockQuantity;
use PHPUnit\Framework\TestCase;

/**
 * Tests the StockQuantity value object which is the pure domain layer
 * for all stock arithmetic.
 */
class StockQuantityTest extends TestCase
{
    public function test_add_increases_quantity(): void
    {
        $a = new StockQuantity(100);
        $b = new StockQuantity(25);

        $result = $a->add($b);

        $this->assertSame(125, $result->value);
    }

    public function test_subtract_decreases_quantity(): void
    {
        $a = new StockQuantity(100);
        $b = new StockQuantity(30);

        $result = $a->subtract($b);

        $this->assertSame(70, $result->value);
    }

    public function test_subtract_throws_on_insufficient_stock(): void
    {
        $a = new StockQuantity(10);
        $b = new StockQuantity(50);

        $this->expectException(\DomainException::class);
        $a->subtract($b);
    }

    public function test_subtract_exact_amount_yields_zero(): void
    {
        $a = new StockQuantity(50);
        $b = new StockQuantity(50);

        $result = $a->subtract($b);
        $this->assertTrue($result->isZero());
    }

    public function test_add_zero_leaves_quantity_unchanged(): void
    {
        $a = new StockQuantity(100);
        $b = new StockQuantity(0);

        $result = $a->add($b);
        $this->assertSame(100, $result->value);
    }

    public function test_constructor_rejects_negative_value(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new StockQuantity(-1);
    }

    public function test_is_zero_returns_true_for_zero(): void
    {
        $this->assertTrue((new StockQuantity(0))->isZero());
    }

    public function test_is_zero_returns_false_for_nonzero(): void
    {
        $this->assertFalse((new StockQuantity(5))->isZero());
    }

    public function test_is_less_than_compares_correctly(): void
    {
        $a = new StockQuantity(5);
        $b = new StockQuantity(10);

        $this->assertTrue($a->isLessThan($b));
        $this->assertFalse($b->isLessThan($a));
    }

    public function test_to_int_returns_raw_value(): void
    {
        $qty = new StockQuantity(42);
        $this->assertSame(42, $qty->toInt());
    }

    public function test_add_returns_new_instance(): void
    {
        $a = new StockQuantity(10);
        $b = new StockQuantity(5);
        $result = $a->add($b);

        $this->assertNotSame($a, $result);
        $this->assertSame(10, $a->value);
    }
}
