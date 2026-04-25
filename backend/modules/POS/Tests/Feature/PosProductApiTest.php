<?php

namespace Modules\POS\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Tests\TestCase;

class PosProductApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    public function test_list_products_returns_paginated_response(): void
    {
        $response = $this->getJson('/api/tenant/pos/products');
        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data', 'meta']);
    }

    public function test_create_product_with_valid_data(): void
    {
        $response = $this->postJson('/api/tenant/pos/products', [
            'name'           => 'Test Widget',
            'purchase_price' => 10.00,
            'sale_price'     => 15.00,
            'amount'         => 100,
            'barcode_number' => '1234567890123',
        ]);
        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Test Widget');

        $this->assertDatabaseHas('products', ['name' => 'Test Widget']);
    }

    public function test_create_product_fails_without_required_fields(): void
    {
        $response = $this->postJson('/api/tenant/pos/products', []);
        $response->assertStatus(422);
    }

    public function test_show_product_returns_correct_data(): void
    {
        $createResponse = $this->postJson('/api/tenant/pos/products', [
            'name'           => 'Show Widget',
            'purchase_price' => 5.00,
            'sale_price'     => 9.00,
            'amount'         => 20,
        ]);
        $id = $createResponse->json('data.id');

        $response = $this->getJson("/api/tenant/pos/products/{$id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $id)
            ->assertJsonPath('data.name', 'Show Widget');
    }

    public function test_update_product(): void
    {
        $createResponse = $this->postJson('/api/tenant/pos/products', [
            'name'           => 'Old Name',
            'purchase_price' => 5.00,
            'sale_price'     => 9.00,
            'amount'         => 10,
        ]);
        $id = $createResponse->json('data.id');

        $response = $this->putJson("/api/tenant/pos/products/{$id}", [
            'name'           => 'New Name',
            'purchase_price' => 5.00,
            'sale_price'     => 12.00,
            'amount'         => 10,
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.sale_price', 12.00);
    }

    public function test_delete_product(): void
    {
        $createResponse = $this->postJson('/api/tenant/pos/products', [
            'name'           => 'Deletable',
            'purchase_price' => 5.00,
            'sale_price'     => 9.00,
            'amount'         => 5,
        ]);
        $id = $createResponse->json('data.id');

        $this->deleteJson("/api/tenant/pos/products/{$id}")->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $id]);
    }

    public function test_bulk_delete_products(): void
    {
        $id1 = $this->postJson('/api/tenant/pos/products', ['name' => 'Bulk1', 'purchase_price' => 1, 'sale_price' => 2, 'amount' => 1])->json('data.id');
        $id2 = $this->postJson('/api/tenant/pos/products', ['name' => 'Bulk2', 'purchase_price' => 1, 'sale_price' => 2, 'amount' => 1])->json('data.id');

        $response = $this->postJson('/api/tenant/pos/products/bulk-delete', ['ids' => [$id1, $id2]]);
        $response->assertStatus(200);

        $this->assertSoftDeleted('products', ['id' => $id1]);
        $this->assertSoftDeleted('products', ['id' => $id2]);
    }

    public function test_search_by_barcode(): void
    {
        $this->postJson('/api/tenant/pos/products', [
            'name'           => 'Barcode Product',
            'purchase_price' => 5.00,
            'sale_price'     => 9.00,
            'amount'         => 5,
            'barcode_number' => 'TESTBARCODE001',
        ]);

        $response = $this->getJson('/api/tenant/pos/products/barcode/TESTBARCODE001');
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Barcode Product');
    }

    public function test_product_not_found_returns_404(): void
    {
        $this->getJson('/api/tenant/pos/products/99999')->assertStatus(404);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->postJson('/api/tenant/pos/products', ['name' => 'X'])->assertStatus(401);
    }
}
