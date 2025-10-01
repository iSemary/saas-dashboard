<?php

namespace Modules\Tenant\Tests\Feature;

use Modules\Tenant\Entities\TenantOwner;
use Modules\Tenant\Services\TenantOwnerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;
use Tests\TestCase;

class TenantOwnerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $tenant;
    protected $tenantOwnerService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->tenant = Tenant::factory()->create();
        $this->tenantOwnerService = app(TenantOwnerService::class);
    }

    /** @test */
    public function it_can_create_a_tenant_owner()
    {
        $tenantOwnerData = [
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'role' => 'owner',
            'is_super_admin' => true,
            'permissions' => ['read.tenant_owners', 'create.tenant_owners'],
            'status' => 'active',
        ];

        $tenantOwner = $this->tenantOwnerService->create($tenantOwnerData);

        $this->assertNotNull($tenantOwner);
        $this->assertEquals($tenantOwnerData['tenant_id'], $tenantOwner->tenant_id);
        $this->assertEquals($tenantOwnerData['user_id'], $tenantOwner->user_id);
        $this->assertEquals($tenantOwnerData['role'], $tenantOwner->role);
        $this->assertTrue($tenantOwner->is_super_admin);
        $this->assertEquals($tenantOwnerData['status'], $tenantOwner->status);
        $this->assertDatabaseHas('tenant_owners', [
            'tenant_id' => $tenantOwnerData['tenant_id'],
            'user_id' => $tenantOwnerData['user_id'],
        ]);
    }

    /** @test */
    public function it_can_update_a_tenant_owner()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
        ]);

        $updateData = [
            'role' => 'admin',
            'is_super_admin' => false,
            'status' => 'inactive',
        ];

        $updated = $this->tenantOwnerService->update($tenantOwner->id, $updateData);

        $this->assertTrue($updated);
        $tenantOwner->refresh();
        $this->assertEquals('admin', $tenantOwner->role);
        $this->assertFalse($tenantOwner->is_super_admin);
        $this->assertEquals('inactive', $tenantOwner->status);
    }

    /** @test */
    public function it_can_soft_delete_a_tenant_owner()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
        ]);

        $deleted = $this->tenantOwnerService->delete($tenantOwner->id);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('tenant_owners', ['id' => $tenantOwner->id]);
    }

    /** @test */
    public function it_can_restore_a_soft_deleted_tenant_owner()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
        ]);

        $tenantOwner->delete();
        $restored = $this->tenantOwnerService->restore($tenantOwner->id);

        $this->assertTrue($restored);
        $this->assertDatabaseHas('tenant_owners', [
            'id' => $tenantOwner->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_get_tenant_owners_for_a_specific_tenant()
    {
        $tenant2 = Tenant::factory()->create();
        $user2 = User::factory()->create();

        // Create tenant owners for different tenants
        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
        ]);

        TenantOwner::factory()->create([
            'tenant_id' => $tenant2->id,
            'user_id' => $user2->id,
        ]);

        $tenantOwners = $this->tenantOwnerService->getTenantOwnersForTenant($this->tenant->id);

        $this->assertCount(1, $tenantOwners);
        $this->assertEquals($this->tenant->id, $tenantOwners->first()->tenant_id);
    }

    /** @test */
    public function it_can_get_super_admins_for_a_tenant()
    {
        $user2 = User::factory()->create();

        // Create regular tenant owner
        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'is_super_admin' => false,
        ]);

        // Create super admin tenant owner
        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user2->id,
            'is_super_admin' => true,
        ]);

        $superAdmins = $this->tenantOwnerService->getSuperAdminsForTenant($this->tenant->id);

        $this->assertCount(1, $superAdmins);
        $this->assertTrue($superAdmins->first()->is_super_admin);
    }

    /** @test */
    public function it_can_promote_user_to_super_admin()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'is_super_admin' => false,
        ]);

        $promoted = $this->tenantOwnerService->promoteToSuperAdmin($tenantOwner->id);

        $this->assertTrue($promoted);
        $tenantOwner->refresh();
        $this->assertTrue($tenantOwner->is_super_admin);
    }

    /** @test */
    public function it_can_demote_user_from_super_admin()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'is_super_admin' => true,
        ]);

        $demoted = $this->tenantOwnerService->demoteFromSuperAdmin($tenantOwner->id);

        $this->assertTrue($demoted);
        $tenantOwner->refresh();
        $this->assertFalse($tenantOwner->is_super_admin);
    }

    /** @test */
    public function it_can_update_tenant_owner_status()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $updated = $this->tenantOwnerService->update($tenantOwner->id, ['status' => 'suspended']);

        $this->assertTrue($updated);
        $tenantOwner->refresh();
        $this->assertEquals('suspended', $tenantOwner->status);
    }

    /** @test */
    public function it_can_update_tenant_owner_permissions()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'permissions' => ['read.tenant_owners'],
        ]);

        $newPermissions = ['read.tenant_owners', 'create.tenant_owners', 'update.tenant_owners'];
        $updated = $this->tenantOwnerService->updatePermissions($tenantOwner->id, $newPermissions);

        $this->assertTrue($updated);
        $tenantOwner->refresh();
        $this->assertEquals($newPermissions, $tenantOwner->permissions);
    }

    /** @test */
    public function it_can_check_if_user_is_tenant_owner()
    {
        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $isOwner = $this->tenantOwnerService->isUserTenantOwner($this->user->id, $this->tenant->id);
        $isNotOwner = $this->tenantOwnerService->isUserTenantOwner(999, $this->tenant->id);

        $this->assertTrue($isOwner);
        $this->assertFalse($isNotOwner);
    }

    /** @test */
    public function it_can_check_if_user_is_super_admin()
    {
        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $isSuperAdmin = $this->tenantOwnerService->isUserSuperAdmin($this->user->id, $this->tenant->id);
        $isNotSuperAdmin = $this->tenantOwnerService->isUserSuperAdmin(999, $this->tenant->id);

        $this->assertTrue($isSuperAdmin);
        $this->assertFalse($isNotSuperAdmin);
    }

    /** @test */
    public function it_can_get_tenant_owners_by_user()
    {
        $tenant2 = Tenant::factory()->create();

        // Create tenant owners for the same user in different tenants
        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        TenantOwner::factory()->create([
            'tenant_id' => $tenant2->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $tenantOwners = $this->tenantOwnerService->getTenantOwnersByUser($this->user->id);

        $this->assertCount(2, $tenantOwners);
        foreach ($tenantOwners as $tenantOwner) {
            $this->assertEquals($this->user->id, $tenantOwner->user_id);
        }
    }

    /** @test */
    public function it_can_assign_user_to_tenant()
    {
        $user2 = User::factory()->create();

        $tenantOwner = $this->tenantOwnerService->assignUserToTenant(
            $user2->id,
            $this->tenant->id,
            ['role' => 'admin', 'is_super_admin' => false]
        );

        $this->assertNotNull($tenantOwner);
        $this->assertEquals($user2->id, $tenantOwner->user_id);
        $this->assertEquals($this->tenant->id, $tenantOwner->tenant_id);
        $this->assertEquals('admin', $tenantOwner->role);
        $this->assertFalse($tenantOwner->is_super_admin);
    }

    /** @test */
    public function it_can_get_dashboard_statistics()
    {
        // Create tenant owners with different statuses
        TenantOwner::factory()->create(['status' => 'active', 'is_super_admin' => true]);
        TenantOwner::factory()->create(['status' => 'active', 'is_super_admin' => false]);
        TenantOwner::factory()->create(['status' => 'inactive']);
        TenantOwner::factory()->create(['status' => 'suspended']);

        $stats = $this->tenantOwnerService->getDashboardStats();

        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('inactive', $stats);
        $this->assertArrayHasKey('suspended', $stats);
        $this->assertArrayHasKey('super_admins', $stats);
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['active']);
        $this->assertEquals(1, $stats['inactive']);
        $this->assertEquals(1, $stats['suspended']);
        $this->assertEquals(1, $stats['super_admins']);
    }

    /** @test */
    public function it_can_search_tenant_owners()
    {
        $user2 = User::factory()->create(['name' => 'John Doe']);
        $user3 = User::factory()->create(['name' => 'Jane Smith']);

        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user2->id,
        ]);

        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user3->id,
        ]);

        $results = $this->tenantOwnerService->search('John');

        $this->assertCount(1, $results);
        $this->assertEquals($user2->id, $results->first()->user_id);
    }

    /** @test */
    public function it_can_filter_tenant_owners_by_role()
    {
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user2->id,
            'role' => 'admin',
        ]);

        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user3->id,
            'role' => 'manager',
        ]);

        $admins = $this->tenantOwnerService->getByRole('admin');
        $managers = $this->tenantOwnerService->getByRole('manager');

        $this->assertCount(1, $admins);
        $this->assertCount(1, $managers);
        $this->assertEquals('admin', $admins->first()->role);
        $this->assertEquals('manager', $managers->first()->role);
    }

    /** @test */
    public function it_can_filter_tenant_owners_by_status()
    {
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user2->id,
            'status' => 'active',
        ]);

        TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user3->id,
            'status' => 'inactive',
        ]);

        $activeOwners = $this->tenantOwnerService->getByStatus('active');
        $inactiveOwners = $this->tenantOwnerService->getByStatus('inactive');

        $this->assertCount(1, $activeOwners);
        $this->assertCount(1, $inactiveOwners);
        $this->assertEquals('active', $activeOwners->first()->status);
        $this->assertEquals('inactive', $inactiveOwners->first()->status);
    }

    /** @test */
    public function it_has_relationships_with_tenant_and_user()
    {
        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(Tenant::class, $tenantOwner->tenant);
        $this->assertInstanceOf(User::class, $tenantOwner->user);
        $this->assertEquals($this->tenant->id, $tenantOwner->tenant->id);
        $this->assertEquals($this->user->id, $tenantOwner->user->id);
    }

    /** @test */
    public function it_has_relationships_with_creator_and_updater()
    {
        $creator = User::factory()->create();
        $updater = User::factory()->create();

        $tenantOwner = TenantOwner::factory()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'created_by' => $creator->id,
            'updated_by' => $updater->id,
        ]);

        $this->assertInstanceOf(User::class, $tenantOwner->creator);
        $this->assertInstanceOf(User::class, $tenantOwner->updater);
        $this->assertEquals($creator->id, $tenantOwner->creator->id);
        $this->assertEquals($updater->id, $tenantOwner->updater->id);
    }
}
