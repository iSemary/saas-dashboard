<?php

namespace Modules\Sales\Tests\Unit;

use Modules\Sales\Domain\Strategies\OrderType\DeliveryOrderStrategy;
use Modules\Sales\Domain\Strategies\OrderType\DineInOrderStrategy;
use Modules\Sales\Domain\Strategies\OrderType\TakeawayOrderStrategy;
use PHPUnit\Framework\TestCase;

class OrderTypeStrategyTest extends TestCase
{
    public function test_takeaway_strategy_returns_takeaway_type(): void
    {
        $strategy = new TakeawayOrderStrategy();
        $this->assertSame('takeaway', $strategy->getType());
    }

    public function test_takeaway_strategy_prepare_sets_order_type(): void
    {
        $strategy = new TakeawayOrderStrategy();
        $result = $strategy->prepare(['total_price' => 50.0]);
        $this->assertSame('takeaway', $result['order_type']);
    }

    public function test_dine_in_strategy_returns_dine_in_type(): void
    {
        $strategy = new DineInOrderStrategy();
        $this->assertSame('dine_in', $strategy->getType());
    }

    public function test_dine_in_strategy_prepare_sets_order_type(): void
    {
        $strategy = new DineInOrderStrategy();
        $result = $strategy->prepare(['total_price' => 50.0]);
        $this->assertSame('dine_in', $result['order_type']);
    }

    public function test_delivery_strategy_returns_delivery_type(): void
    {
        $strategy = new DeliveryOrderStrategy();
        $this->assertSame('delivery', $strategy->getType());
    }

    public function test_delivery_strategy_prepare_merges_delivery_type(): void
    {
        $strategy = new DeliveryOrderStrategy();
        $result = $strategy->prepare([
            'total_price'      => 50.0,
            'delivery_name'    => 'John Doe',
            'delivery_phone'   => '+20123456789',
            'delivery_address' => '123 Main St',
        ]);
        $this->assertSame('delivery', $result['order_type']);
        $this->assertSame('John Doe', $result['delivery_name']);
    }

    public function test_prepare_preserves_existing_data(): void
    {
        $strategy = new TakeawayOrderStrategy();
        $result = $strategy->prepare(['total_price' => 99.0, 'some_key' => 'val']);
        $this->assertSame(99.0, $result['total_price']);
        $this->assertSame('val', $result['some_key']);
    }
}
