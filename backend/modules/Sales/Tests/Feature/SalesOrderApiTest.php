<?php

namespace Modules\Sales\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Modules\Sales\Domain\Entities\SalesOrder;
use Tests\TestCase;

class SalesOrderApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    private function validOrderPayload(array $overrides = []): array
    {
        return array_merge([
            'products'    => [
                ['product_id' => 1, 'quantity' => 2, 'unit_price' => 10.00, 'total_price' => 20.00],
            ],
            'total_price' => 20.00,
            'amount_paid' => 20.00,
            'pay_method'  => 'cash',
            'order_type'  => 'takeaway',
        ], $overrides);
    }

    public function test_list_orders_returns_paginated_response(): void
    {
        $response = $this->getJson('/api/tenant/sales/orders');
        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);
    }

    public function test_daily_summary_returns_expected_structure(): void
    {
        $response = $this->getJson('/api/tenant/sales/summary');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['date', 'total_orders', 'total_revenue', 'total_paid', 'by_method'],
            ]);
    }

    public function test_create_cash_takeaway_order(): void
    {
        $response = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload());
        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.pay_method', 'cash')
            ->assertJsonPath('data.order_type', 'takeaway')
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('sales_orders', ['pay_method' => 'cash', 'status' => 'completed']);
    }

    public function test_create_card_order_requires_transaction_number(): void
    {
        $response = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload([
            'pay_method' => 'card',
        ]));
        $response->assertStatus(422);
    }

    public function test_create_card_order_with_transaction_number(): void
    {
        $response = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload([
            'pay_method'         => 'card',
            'transaction_number' => 'TXN-001',
        ]));
        $response->assertStatus(201)
            ->assertJsonPath('data.pay_method', 'card');
    }

    public function test_create_installment_order_requires_months_and_amount(): void
    {
        $response = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload([
            'pay_method' => 'installment',
        ]));
        $response->assertStatus(422);
    }

    public function test_create_installment_order_creates_installment_record(): void
    {
        $response = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload([
            'pay_method'     => 'installment',
            'total_months'   => 6,
            'monthly_amount' => 20.00 / 6,
        ]));
        $response->assertStatus(201);
        $orderId = $response->json('data.id');
        $this->assertDatabaseHas('sales_order_installments', ['order_id' => $orderId]);
    }

    public function test_create_delivery_order_creates_delivery_record(): void
    {
        $response = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload([
            'order_type'       => 'delivery',
            'delivery_name'    => 'John Doe',
            'delivery_phone'   => '+201234567890',
            'delivery_address' => '123 Main St',
        ]));
        $response->assertStatus(201);
        $orderId = $response->json('data.id');
        $this->assertDatabaseHas('sales_deliveries', ['order_id' => $orderId]);
    }

    public function test_cancel_order_changes_status(): void
    {
        $createResp = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload());
        $orderId = $createResp->json('data.id');

        $response = $this->patchJson("/api/tenant/sales/orders/{$orderId}/cancel");
        $response->assertStatus(200);
        $this->assertDatabaseHas('sales_orders', ['id' => $orderId, 'status' => 'cancelled']);
    }

    public function test_cancel_already_cancelled_order_returns_422(): void
    {
        $createResp = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload());
        $orderId = $createResp->json('data.id');

        $this->patchJson("/api/tenant/sales/orders/{$orderId}/cancel");
        $response = $this->patchJson("/api/tenant/sales/orders/{$orderId}/cancel");
        $response->assertStatus(422);
    }

    public function test_delete_order(): void
    {
        $createResp = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload());
        $orderId = $createResp->json('data.id');

        $this->deleteJson("/api/tenant/sales/orders/{$orderId}")->assertStatus(200);
        $this->assertSoftDeleted('sales_orders', ['id' => $orderId]);
    }

    public function test_bulk_delete_orders(): void
    {
        $id1 = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload())->json('data.id');
        $id2 = $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload())->json('data.id');

        $this->postJson('/api/tenant/sales/orders/bulk-delete', ['ids' => [$id1, $id2]])->assertStatus(200);
        $this->assertSoftDeleted('sales_orders', ['id' => $id1]);
        $this->assertSoftDeleted('sales_orders', ['id' => $id2]);
    }

    public function test_create_order_missing_products_fails(): void
    {
        $response = $this->postJson('/api/tenant/sales/orders', [
            'total_price' => 20.00,
            'amount_paid' => 20.00,
            'pay_method'  => 'cash',
            'order_type'  => 'takeaway',
        ]);
        $response->assertStatus(422);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->postJson('/api/tenant/sales/orders', $this->validOrderPayload())->assertStatus(401);
    }
}
