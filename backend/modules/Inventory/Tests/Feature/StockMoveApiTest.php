<?php

namespace Modules\Inventory\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Tests\TestCase;

class StockMoveApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected int $warehouseId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');

        $resp = $this->postJson('/api/tenant/inventory/warehouses', [
            'name'      => 'Test Warehouse',
            'code'      => 'WH-TEST-' . uniqid(),
            'is_active' => true,
        ]);
        $this->warehouseId = $resp->json('data.id');
    }

    private function createMoveViaApi(array $overrides = []): int
    {
        $resp = $this->postJson('/api/tenant/inventory/stock-moves', array_merge([
            'product_id'   => 1,
            'warehouse_id' => $this->warehouseId,
            'move_type'    => 'in',
            'quantity'     => 50,
            'unit_cost'    => 10.00,
            'date'         => now()->toDateString(),
        ], $overrides));
        $resp->assertStatus(201);
        return $resp->json('data.id');
    }

    public function test_list_stock_moves(): void
    {
        $this->getJson('/api/tenant/inventory/stock-moves')
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);
    }

    public function test_create_inbound_stock_move(): void
    {
        $response = $this->postJson('/api/tenant/inventory/stock-moves', [
            'product_id'   => 1,
            'warehouse_id' => $this->warehouseId,
            'move_type'    => 'in',
            'quantity'     => 100,
            'unit_cost'    => 5.00,
            'date'         => now()->toDateString(),
        ]);
        $response->assertStatus(201)
            ->assertJsonPath('data.move_type', 'in')
            ->assertJsonPath('data.state', 'draft');

        $this->assertDatabaseHas('stock_moves', [
            'warehouse_id' => $this->warehouseId,
            'move_type'    => 'in',
            'quantity'     => 100,
        ]);
    }

    public function test_create_stock_move_requires_product_and_warehouse(): void
    {
        $this->postJson('/api/tenant/inventory/stock-moves', ['quantity' => 10])->assertStatus(422);
    }

    public function test_confirm_stock_move(): void
    {
        $id = $this->createMoveViaApi();

        $response = $this->patchJson("/api/tenant/inventory/stock-moves/{$id}/confirm");
        $response->assertStatus(200);
        $this->assertDatabaseHas('stock_moves', ['id' => $id, 'state' => 'confirmed']);
    }

    public function test_complete_stock_move(): void
    {
        $id = $this->createMoveViaApi();

        $this->patchJson("/api/tenant/inventory/stock-moves/{$id}/confirm");
        $response = $this->patchJson("/api/tenant/inventory/stock-moves/{$id}/complete");
        $response->assertStatus(200);
        $this->assertDatabaseHas('stock_moves', ['id' => $id, 'state' => 'done']);
    }

    public function test_complete_done_move_returns_422(): void
    {
        $id = $this->createMoveViaApi();
        $this->patchJson("/api/tenant/inventory/stock-moves/{$id}/confirm");
        $this->patchJson("/api/tenant/inventory/stock-moves/{$id}/complete");

        $this->patchJson("/api/tenant/inventory/stock-moves/{$id}/complete")->assertStatus(422);
    }

    public function test_cancel_stock_move(): void
    {
        $id = $this->createMoveViaApi();

        $response = $this->patchJson("/api/tenant/inventory/stock-moves/{$id}/cancel");
        $response->assertStatus(200);
        $this->assertDatabaseHas('stock_moves', ['id' => $id, 'state' => 'cancel']);
    }

    public function test_delete_stock_move(): void
    {
        $id = $this->createMoveViaApi();
        $this->deleteJson("/api/tenant/inventory/stock-moves/{$id}")->assertStatus(200);
        $this->assertSoftDeleted('stock_moves', ['id' => $id]);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->postJson('/api/tenant/inventory/stock-moves', [])->assertStatus(401);
    }
}
