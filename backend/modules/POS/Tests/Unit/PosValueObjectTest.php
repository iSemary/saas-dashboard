<?php

namespace Modules\POS\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\POS\Domain\ValueObjects\Money;
use Modules\POS\Domain\ValueObjects\BarcodeValue;
use Modules\POS\Domain\ValueObjects\StockQuantity;
use Modules\POS\Domain\Enums\ProductType;
use Modules\POS\Domain\Enums\StockDirection;
use Modules\POS\Domain\Enums\StockModelType;

class PosValueObjectTest extends TestCase
{
    // ── Money ─────────────────────────────────────────────────────

    public function test_money_construction(): void
    {
        $money = new Money(100.0);
        $this->assertSame(100.0, $money->amount);
        $this->assertSame('USD', $money->currency);
    }

    public function test_money_rejects_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(-1.0);
    }

    public function test_money_add(): void
    {
        $a = new Money(50.0);
        $b = new Money(30.0);
        $result = $a->add($b);
        $this->assertSame(80.0, $result->amount);
    }

    public function test_money_subtract_clamps_to_zero(): void
    {
        $a = new Money(10.0);
        $b = new Money(50.0);
        $result = $a->subtract($b);
        $this->assertSame(0.0, $result->amount);
    }

    public function test_money_multiply(): void
    {
        $money = new Money(100.0);
        $result = $money->multiply(1.5);
        $this->assertSame(150.0, $result->amount);
    }

    public function test_money_apply_discount_percent(): void
    {
        $money = new Money(200.0);
        $result = $money->applyDiscountPercent(25.0);
        $this->assertSame(150.0, $result->amount);
    }

    public function test_money_equals(): void
    {
        $a = new Money(100.0);
        $b = new Money(100.0);
        $this->assertTrue($a->equals($b));
    }

    public function test_money_equals_different_currency(): void
    {
        $a = new Money(100.0, 'USD');
        $b = new Money(100.0, 'EUR');
        $this->assertFalse($a->equals($b));
    }

    public function test_money_to_float(): void
    {
        $money = new Money(42.5);
        $this->assertSame(42.5, $money->toFloat());
    }

    public function test_money_to_string(): void
    {
        $money = new Money(1234.56);
        $this->assertSame('1,234.56', (string) $money);
    }

    // ── BarcodeValue ──────────────────────────────────────────────

    public function test_barcode_value_construction(): void
    {
        $barcode = new BarcodeValue('ABC-123');
        $this->assertSame('ABC-123', $barcode->value);
    }

    public function test_barcode_value_rejects_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BarcodeValue('');
    }

    public function test_barcode_value_rejects_whitespace_only(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BarcodeValue('   ');
    }

    public function test_barcode_value_rejects_invalid_chars(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new BarcodeValue('ABC@123');
    }

    public function test_barcode_value_allows_alphanumeric_dash_underscore_dot(): void
    {
        $barcode = new BarcodeValue('SKU_123-45.67');
        $this->assertSame('SKU_123-45.67', $barcode->value);
    }

    public function test_barcode_value_equals(): void
    {
        $a = new BarcodeValue('ABC-123');
        $b = new BarcodeValue('ABC-123');
        $this->assertTrue($a->equals($b));
    }

    public function test_barcode_value_not_equals(): void
    {
        $a = new BarcodeValue('ABC-123');
        $b = new BarcodeValue('XYZ-789');
        $this->assertFalse($a->equals($b));
    }

    public function test_barcode_value_to_string(): void
    {
        $barcode = new BarcodeValue('ABC-123');
        $this->assertSame('ABC-123', (string) $barcode);
    }

    // ── StockQuantity ────────────────────────────────────────────

    public function test_stock_quantity_creation(): void
    {
        $qty = new StockQuantity(10);
        $this->assertSame(10, $qty->value);
        $this->assertSame(10, $qty->toInt());
    }

    public function test_stock_quantity_zero(): void
    {
        $qty = new StockQuantity(0);
        $this->assertTrue($qty->isZero());
    }

    public function test_stock_quantity_add(): void
    {
        $result = (new StockQuantity(5))->add(new StockQuantity(3));
        $this->assertSame(8, $result->value);
    }

    public function test_stock_quantity_subtract(): void
    {
        $result = (new StockQuantity(10))->subtract(new StockQuantity(3));
        $this->assertSame(7, $result->value);
    }

    public function test_stock_quantity_subtract_insufficient_throws(): void
    {
        $this->expectException(\DomainException::class);
        (new StockQuantity(2))->subtract(new StockQuantity(5));
    }

    public function test_stock_quantity_negative_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new StockQuantity(-1);
    }

    public function test_stock_quantity_is_less_than(): void
    {
        $this->assertTrue((new StockQuantity(3))->isLessThan(new StockQuantity(5)));
        $this->assertFalse((new StockQuantity(5))->isLessThan(new StockQuantity(3)));
    }

    // ── ProductType (enum) ───────────────────────────────────────

    public function test_product_type_values(): void
    {
        $this->assertSame(1, ProductType::Regular->value);
        $this->assertSame(2, ProductType::Wholesale->value);
    }

    public function test_product_type_all_cases_covered(): void
    {
        $this->assertCount(2, ProductType::cases());
    }

    // ── StockDirection (enum) ─────────────────────────────────────

    public function test_stock_direction_values(): void
    {
        $this->assertSame('increment', StockDirection::Increment->value);
        $this->assertSame('decrement', StockDirection::Decrement->value);
    }

    public function test_stock_direction_all_cases_covered(): void
    {
        $this->assertCount(2, StockDirection::cases());
    }

    // ── StockModelType (enum) ─────────────────────────────────────

    public function test_stock_model_type_values(): void
    {
        $this->assertSame('order', StockModelType::Order->value);
        $this->assertSame('returned', StockModelType::Returned->value);
        $this->assertSame('damaged', StockModelType::Damaged->value);
        $this->assertSame('offer_price', StockModelType::OfferPrice->value);
        $this->assertSame('purchases', StockModelType::Purchase->value);
    }

    public function test_stock_model_type_all_cases_covered(): void
    {
        $this->assertCount(5, StockModelType::cases());
    }
}
