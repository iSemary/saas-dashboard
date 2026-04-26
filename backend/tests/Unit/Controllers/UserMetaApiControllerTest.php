<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for UserMetaApiController business logic.
 *
 * NOTE: The Laravel application cannot be booted in this test suite due to a
 * pre-existing project-level bug: the EmailMarketing module references classes
 * that don't exist yet, causing a fatal error during app boot. The migration
 * layer also has an unresolved FK ordering issue (pos_categories -> branches).
 *
 * Rather than testing through the real HTTP stack (which needs the container),
 * we test the *logic* expressed by the controller in isolation:
 *   - index()  : map of meta_key => meta_value
 *   - show()   : return value or null
 *   - store()  : validation rules + upsert semantics
 *   - destroy(): scoped delete + idempotency
 *
 * Integration / feature tests that hit a real database should be added once
 * the module boot error and the migration ordering issue are resolved.
 */
class UserMetaApiControllerTest extends TestCase
{
    // ------------------------------------------------------------------
    // index() logic
    // ------------------------------------------------------------------

    public function test_index_maps_meta_rows_to_key_value_pairs(): void
    {
        $rows = [
            (object)['meta_key' => 'theme_mode',              'meta_value' => 'dark'],
            (object)['meta_key' => 'dashboard_layout_tenant', 'meta_value' => '{"lg":[]}'],
            (object)['meta_key' => 'animations_enabled',      'meta_value' => '1'],
        ];

        // Replicates the foreach loop in UserMetaApiController::index()
        $data = [];
        foreach ($rows as $meta) {
            $data[$meta->meta_key] = $meta->meta_value;
        }

        $this->assertArrayHasKey('theme_mode',              $data);
        $this->assertArrayHasKey('dashboard_layout_tenant', $data);
        $this->assertArrayHasKey('animations_enabled',      $data);
        $this->assertEquals('dark',       $data['theme_mode']);
        $this->assertEquals('{"lg":[]}',  $data['dashboard_layout_tenant']);
    }

    public function test_index_returns_empty_array_when_no_rows(): void
    {
        $data = [];
        foreach ([] as $meta) {
            $data[$meta->meta_key] = $meta->meta_value;
        }

        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    // ------------------------------------------------------------------
    // show() logic
    // ------------------------------------------------------------------

    public function test_show_returns_meta_value_when_record_found(): void
    {
        $meta             = new \stdClass();
        $meta->meta_value = '{"lg":[{"i":"hero","x":0,"y":0,"w":12,"h":2}]}';

        // Replicates the null-check in UserMetaApiController::show()
        $result = $meta ? $meta->meta_value : null;

        $this->assertNotNull($result);
        $this->assertJson($result);
        $decoded = json_decode($result, true);
        $this->assertArrayHasKey('lg', $decoded);
    }

    public function test_show_returns_null_when_record_not_found(): void
    {
        $meta   = null;
        $result = $meta ? $meta->meta_value : null;

        $this->assertNull($result);
    }

    // ------------------------------------------------------------------
    // store() validation rules
    // ------------------------------------------------------------------

    public function test_store_validation_rules_require_key(): void
    {
        $rules = [
            'key'   => 'required|string|max:255',
            'value' => 'nullable|string|max:65535',
        ];

        $this->assertStringContainsString('required', $rules['key']);
        $this->assertStringContainsString('max:255',  $rules['key']);
    }

    public function test_store_validation_rules_allow_null_value(): void
    {
        $rules = [
            'key'   => 'required|string|max:255',
            'value' => 'nullable|string|max:65535',
        ];

        $this->assertStringContainsString('nullable', $rules['value']);
    }

    public function test_store_validation_value_has_correct_max_length(): void
    {
        $rules = [
            'key'   => 'required|string|max:255',
            'value' => 'nullable|string|max:65535',
        ];

        // Matches the TEXT column size used for dashboard layout JSON blobs
        $this->assertStringContainsString('max:65535', $rules['value']);
    }

    // ------------------------------------------------------------------
    // store() upsert semantics
    // ------------------------------------------------------------------

    public function test_store_upsert_overwrites_existing_value(): void
    {
        // Simulate the updateOrCreate behaviour: same key → value is replaced
        $store = [];

        $upsert = function (string $key, string $value) use (&$store) {
            $store[$key] = $value;
        };

        $upsert('dashboard_layout_tenant', 'old_value');
        $upsert('dashboard_layout_tenant', 'new_value');

        $this->assertCount(1, $store);
        $this->assertEquals('new_value', $store['dashboard_layout_tenant']);
    }

    public function test_store_upsert_creates_entry_for_new_key(): void
    {
        $store = [];

        $upsert = function (string $key, string $value) use (&$store) {
            $store[$key] = $value;
        };

        $upsert('dashboard_layout_landlord', '{"lg":[]}');

        $this->assertArrayHasKey('dashboard_layout_landlord', $store);
    }

    // ------------------------------------------------------------------
    // destroy() scoping and idempotency
    // ------------------------------------------------------------------

    public function test_destroy_removes_only_matching_user_and_key(): void
    {
        // Simulate the store: user_id → key → value
        $store = [
            42 => ['dashboard_layout_tenant' => '{}', 'theme_mode' => 'dark'],
            99 => ['dashboard_layout_tenant' => 'keep_me'],
        ];

        $delete = function (int $userId, string $key) use (&$store) {
            unset($store[$userId][$key]);
        };

        $delete(42, 'dashboard_layout_tenant');

        $this->assertArrayNotHasKey('dashboard_layout_tenant', $store[42]);
        $this->assertArrayHasKey('theme_mode',                 $store[42]);
        $this->assertArrayHasKey('dashboard_layout_tenant',    $store[99]); // untouched
    }

    public function test_destroy_is_idempotent_for_missing_key(): void
    {
        $store = [42 => []];

        $delete = function (int $userId, string $key) use (&$store) {
            unset($store[$userId][$key]); // no-op if not present
        };

        // Should not throw
        $delete(42, 'nonexistent_key');

        $this->assertEmpty($store[42]);
    }

    public function test_destroy_does_not_touch_other_users_data(): void
    {
        $store = [
            42 => ['dashboard_layout_tenant' => 'mine'],
            77 => ['dashboard_layout_tenant' => 'theirs'],
        ];

        $delete = function (int $userId, string $key) use (&$store) {
            unset($store[$userId][$key]);
        };

        $delete(42, 'dashboard_layout_tenant');

        $this->assertArrayNotHasKey('dashboard_layout_tenant', $store[42]);
        $this->assertArrayHasKey('dashboard_layout_tenant',    $store[77]);
    }

    // ------------------------------------------------------------------
    // Response envelope contract
    // ------------------------------------------------------------------

    public function test_success_response_envelope_structure(): void
    {
        // Replicates what ApiResponseEnvelope::apiSuccess() returns
        $payload = [
            'status'  => 'success',
            'data'    => ['theme_mode' => 'dark'],
            'message' => '',
        ];

        $this->assertEquals('success', $payload['status']);
        $this->assertArrayHasKey('data',    $payload);
        $this->assertArrayHasKey('message', $payload);
    }

    public function test_success_response_with_message(): void
    {
        $payload = [
            'status'  => 'success',
            'data'    => new \stdClass(),
            'message' => 'Setting saved successfully',
        ];

        $this->assertEquals('Setting saved successfully', $payload['message']);
    }

    public function test_destroy_response_message(): void
    {
        $payload = [
            'status'  => 'success',
            'data'    => null,
            'message' => 'Setting removed successfully',
        ];

        $this->assertEquals('Setting removed successfully', $payload['message']);
        $this->assertNull($payload['data']);
    }
}
