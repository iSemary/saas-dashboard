<?php

namespace Modules\Inventory\Tests\Unit;

use Modules\Inventory\Domain\Enums\StockMoveState;
use Modules\Inventory\Domain\Enums\StockMoveType;
use PHPUnit\Framework\TestCase;

class InventoryEnumTest extends TestCase
{
    public function test_stock_move_type_values(): void
    {
        $this->assertSame('in', StockMoveType::In->value);
        $this->assertSame('out', StockMoveType::Out->value);
        $this->assertSame('transfer', StockMoveType::Transfer->value);
        $this->assertSame('adjust', StockMoveType::Adjust->value);
    }

    public function test_stock_move_state_values(): void
    {
        $this->assertSame('draft', StockMoveState::Draft->value);
        $this->assertSame('confirmed', StockMoveState::Confirmed->value);
        $this->assertSame('done', StockMoveState::Done->value);
        $this->assertSame('cancel', StockMoveState::Cancelled->value);
    }

    public function test_stock_move_type_from_valid_value(): void
    {
        $type = StockMoveType::from('in');
        $this->assertSame(StockMoveType::In, $type);
    }

    public function test_stock_move_type_from_invalid_value_throws(): void
    {
        $this->expectException(\ValueError::class);
        StockMoveType::from('invalid_type');
    }

    public function test_stock_move_state_from_valid_value(): void
    {
        $state = StockMoveState::from('done');
        $this->assertSame(StockMoveState::Done, $state);
    }

    public function test_stock_move_state_try_from_invalid_returns_null(): void
    {
        $state = StockMoveState::tryFrom('nonexistent');
        $this->assertNull($state);
    }

    public function test_all_stock_move_types_are_covered(): void
    {
        $cases = StockMoveType::cases();
        $values = array_map(fn ($c) => $c->value, $cases);

        $this->assertContains('in', $values);
        $this->assertContains('out', $values);
        $this->assertContains('transfer', $values);
        $this->assertContains('adjust', $values);
        $this->assertCount(4, $cases);
    }

    public function test_all_stock_move_states_are_covered(): void
    {
        $cases = StockMoveState::cases();
        $this->assertCount(4, $cases);
    }
}
