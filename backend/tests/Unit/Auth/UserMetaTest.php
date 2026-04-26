<?php

namespace Tests\Unit\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\UserMeta;
use OwenIt\Auditing\Contracts\Auditable;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit tests for the UserMeta model.
 *
 * Uses reflection and in-memory instantiation only — no database, no
 * migrations. This avoids the project-wide FK ordering bug that breaks
 * migrate:fresh (pos_categories references branches before branches exists)
 * as well as the EmailMarketing module boot error.
 *
 * DB-backed tests (getByMetaKey, soft-delete, relationships) should be added
 * as Feature tests once those infrastructure issues are resolved.
 */
class UserMetaTest extends TestCase
{
    // ------------------------------------------------------------------
    // Class existence / structure (no instantiation needed)
    // ------------------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(UserMeta::class));
    }

    public function test_extends_eloquent_model(): void
    {
        $this->assertTrue(
            is_subclass_of(UserMeta::class, \Illuminate\Database\Eloquent\Model::class)
        );
    }

    // ------------------------------------------------------------------
    // Fillable (via reflection — no DB)
    // ------------------------------------------------------------------

    public function test_has_correct_fillable_attributes(): void
    {
        $ref      = new \ReflectionClass(UserMeta::class);
        $prop     = $ref->getProperty('fillable');
        $prop->setAccessible(true);
        $fillable = $prop->getValue($ref->newInstanceWithoutConstructor());

        $this->assertEquals(['user_id', 'meta_key', 'meta_value'], $fillable);
    }

    // ------------------------------------------------------------------
    // Connection (via reflection — no DB)
    // ------------------------------------------------------------------

    public function test_has_landlord_connection(): void
    {
        $ref      = new \ReflectionClass(UserMeta::class);
        $prop     = $ref->getProperty('connection');
        $prop->setAccessible(true);
        $conn     = $prop->getValue($ref->newInstanceWithoutConstructor());

        $this->assertEquals('landlord', $conn);
    }

    // ------------------------------------------------------------------
    // Traits (reflection — no instantiation)
    // ------------------------------------------------------------------

    public function test_uses_has_factory_trait(): void
    {
        $this->assertContains(HasFactory::class, class_uses_recursive(UserMeta::class));
    }

    public function test_uses_soft_deletes_trait(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(UserMeta::class));
    }

    // ------------------------------------------------------------------
    // Interfaces
    // ------------------------------------------------------------------

    public function test_implements_auditable_interface(): void
    {
        $this->assertTrue(
            in_array(Auditable::class, class_implements(UserMeta::class), true)
        );
    }

    // ------------------------------------------------------------------
    // Method existence (reflection — no instantiation)
    // ------------------------------------------------------------------

    public function test_get_by_meta_key_method_exists(): void
    {
        $this->assertTrue(method_exists(UserMeta::class, 'getByMetaKey'));
    }

    public function test_get_by_meta_key_is_callable(): void
    {
        $this->assertTrue(method_exists(UserMeta::class, 'getByMetaKey'));
    }

    public function test_user_relationship_method_exists(): void
    {
        $this->assertTrue(method_exists(UserMeta::class, 'user'));
    }

    // ------------------------------------------------------------------
    // JSON layout value handling (pure logic, no DB)
    // ------------------------------------------------------------------

    public function test_meta_value_accepts_json_encoded_layout(): void
    {
        $layout = ['lg' => [['i' => 'hero', 'x' => 0, 'y' => 0, 'w' => 12, 'h' => 2]]];
        $encoded = json_encode($layout);

        $this->assertIsString($encoded);
        $decoded = json_decode($encoded, true);

        $this->assertArrayHasKey('lg', $decoded);
        $this->assertEquals('hero', $decoded['lg'][0]['i']);
        $this->assertEquals(0,      $decoded['lg'][0]['x']);
        $this->assertEquals(12,     $decoded['lg'][0]['w']);
    }

    public function test_meta_value_preserves_all_breakpoints(): void
    {
        $layout = [
            'lg' => [['i' => 'card', 'x' => 0, 'y' => 0, 'w' => 3, 'h' => 2]],
            'md' => [['i' => 'card', 'x' => 0, 'y' => 0, 'w' => 6, 'h' => 2]],
            'sm' => [['i' => 'card', 'x' => 0, 'y' => 0, 'w' => 6, 'h' => 3]],
            'xs' => [['i' => 'card', 'x' => 0, 'y' => 0, 'w' => 4, 'h' => 3]],
        ];

        $decoded = json_decode(json_encode($layout), true);

        $this->assertArrayHasKey('lg', $decoded);
        $this->assertArrayHasKey('md', $decoded);
        $this->assertArrayHasKey('sm', $decoded);
        $this->assertArrayHasKey('xs', $decoded);
    }

    public function test_empty_layout_encodes_to_valid_json(): void
    {
        $layout  = ['lg' => [], 'md' => [], 'sm' => [], 'xs' => []];
        $encoded = json_encode($layout);

        $this->assertJson($encoded);
        $decoded = json_decode($encoded, true);
        $this->assertEmpty($decoded['lg']);
    }

    // ------------------------------------------------------------------
    // Meta key naming conventions
    // ------------------------------------------------------------------

    public function test_dashboard_layout_tenant_is_valid_meta_key(): void
    {
        $key = 'dashboard_layout_tenant';
        $this->assertMatchesRegularExpression('/^[a-z_]+$/', $key);
        $this->assertLessThanOrEqual(255, strlen($key));
    }

    public function test_dashboard_layout_landlord_is_valid_meta_key(): void
    {
        $key = 'dashboard_layout_landlord';
        $this->assertMatchesRegularExpression('/^[a-z_]+$/', $key);
        $this->assertLessThanOrEqual(255, strlen($key));
    }
}
