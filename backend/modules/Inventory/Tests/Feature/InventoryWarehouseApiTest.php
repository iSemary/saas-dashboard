<?php

namespace Modules\Inventory\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Tests\TestCase;

class InventoryWarehouseApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    private function createWarehouseViaApi(array $overrides = []): int
    {
        $resp = $this->postJson('/api/tenant/inventory/warehouses', array_merge([
            'name'      => 'Test Warehouse',
            'code'      => 'WH-' . uniqid(),
            'is_active' => true,
        ], $overrides));
        $resp->assertStatus(201);
        return $resp->json('data.id');
    }

    public function test_list_warehouses_returns_paginated_response(): void
    {
        $response = $this->getJson('/api/tenant/inventory/warehouses');
        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);
    }

    public function test_create_warehouse_with_valid_data(): void
    {
        $response = $this->postJson('/api/tenant/inventory/warehouses', [
            'name'      => 'Main Warehouse',
            'code'      => 'WH-001',
            'city'      => 'Cairo',
            'is_active' => true,
        ]);
        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Main Warehouse')
            ->assertJsonPath('data.code', 'WH-001');

        $this->assertDatabaseHas('warehouses', ['name' => 'Main Warehouse', 'code' => 'WH-001']);
    }

    public function test_create_warehouse_requires_name_and_code(): void
    {
        $this->postJson('/api/tenant/inventory/warehouses', [])->assertStatus(422);
    }

    public function test_show_warehouse(): void
    {
        $id = $this->createWarehouseViaApi(['name' => 'Show WH']);

        $response = $this->getJson("/api/tenant/inventory/warehouses/{$id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $id)
            ->assertJsonPath('data.name', 'Show WH');
    }

    public function test_update_warehouse(): void
    {
        $id = $this->createWarehouseViaApi(['name' => 'Old WH']);

        $response = $this->putJson("/api/tenant/inventory/warehouses/{$id}", [
            'name' => 'Updated WH',
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated WH');
    }

    public function test_delete_non_default_warehouse(): void
    {
        $id = $this->createWarehouseViaApi(['name' => 'Deletable WH', 'is_default' => false]);

        $this->deleteJson("/api/tenant/inventory/warehouses/{$id}")->assertStatus(200);
        $this->assertSoftDeleted('warehouses', ['id' => $id]);
    }

    public function test_delete_default_warehouse_returns_422(): void
    {
        $id = $this->createWarehouseViaApi(['name' => 'Default WH', 'is_default' => true]);

        $this->deleteJson("/api/tenant/inventory/warehouses/{$id}")->assertStatus(422);
    }

    public function test_stock_summary_returns_expected_structure(): void
    {
        $id = $this->createWarehouseViaApi();

        $response = $this->getJson("/api/tenant/inventory/warehouses/{$id}/stock-summary");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['warehouse_id', 'inbound', 'outbound', 'balance'],
            ]);
    }

    public function test_list_all_warehouses_without_pagination(): void
    {
        $this->createWarehouseViaApi(['name' => 'WH All 1']);
        $this->createWarehouseViaApi(['name' => 'WH All 2']);

        $response = $this->getJson('/api/tenant/inventory/warehouses?all=true');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->postJson('/api/tenant/inventory/warehouses', ['name' => 'X'])->assertStatus(401);
    }
}
