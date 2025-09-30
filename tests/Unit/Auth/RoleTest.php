<?php

namespace Tests\Unit\Auth;

use Tests\Unit\BaseEntityTest;
use Modules\Auth\Entities\Role;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleTest extends BaseEntityTest
{
    protected string $modelClass = Role::class;
    protected string $tableName = 'roles';

    protected array $expectedTraits = [
        SoftDeletes::class,
    ];

    protected array $expectedInterfaces = [
        SpatieRole::class,
    ];

    protected array $sampleData = [
        'name' => 'test_role',
        'guard_name' => 'web',
    ];

    /**
     * Test that the model extends SpatieRole
     */
    public function test_extends_spatie_role(): void
    {
        $role = new Role();
        $this->assertInstanceOf(SpatieRole::class, $role);
    }

    /**
     * Test that the model can be created with all required fields
     */
    public function test_can_be_created_with_all_required_fields(): void
    {
        $role = Role::create($this->sampleData);
        
        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals($this->sampleData['name'], $role->name);
        $this->assertEquals($this->sampleData['guard_name'], $role->guard_name);
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        $role = Role::create($this->sampleData);
        
        $updateData = [
            'name' => 'updated_role',
        ];
        
        $role->update($updateData);
        
        $this->assertEquals($updateData['name'], $role->fresh()->name);
    }

    /**
     * Test that the model can be soft deleted
     */
    public function test_can_be_soft_deleted(): void
    {
        $role = Role::create($this->sampleData);
        $roleId = $role->id;
        
        $role->delete();
        
        $this->assertSoftDeleted('roles', ['id' => $roleId]);
        $this->assertNull(Role::find($roleId));
        $this->assertNotNull(Role::withTrashed()->find($roleId));
    }

    /**
     * Test that the model can be restored
     */
    public function test_can_be_restored(): void
    {
        $role = Role::create($this->sampleData);
        $roleId = $role->id;
        
        $role->delete();
        $role->restore();
        
        $this->assertDatabaseHas('roles', [
            'id' => $roleId,
            'deleted_at' => null
        ]);
        $this->assertNotNull(Role::find($roleId));
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        $role = Role::factory()->create();
        
        $this->assertInstanceOf(Role::class, $role);
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /**
     * Test role permissions relationship
     */
    public function test_permissions_relationship(): void
    {
        $role = Role::create($this->sampleData);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $role->permissions());
    }

    /**
     * Test role users relationship
     */
    public function test_users_relationship(): void
    {
        $role = Role::create($this->sampleData);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $role->users());
    }

    /**
     * Test unique name constraint
     */
    public function test_unique_name_constraint(): void
    {
        Role::create($this->sampleData);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Role::create($this->sampleData);
    }

    /**
     * Test guard name validation
     */
    public function test_guard_name_validation(): void
    {
        $role = Role::create(array_merge($this->sampleData, ['guard_name' => 'api']));
        
        $this->assertEquals('api', $role->guard_name);
    }

    /**
     * Test role name validation
     */
    public function test_role_name_validation(): void
    {
        $validNames = ['admin', 'user', 'moderator', 'test_role_123'];
        
        foreach ($validNames as $name) {
            $role = Role::create(array_merge($this->sampleData, ['name' => $name]));
            $this->assertEquals($name, $role->name);
            $role->delete(); // Clean up
        }
    }

    /**
     * Test role with permissions
     */
    public function test_role_with_permissions(): void
    {
        $role = Role::create($this->sampleData);
        
        // Create a test permission
        $permission = \Spatie\Permission\Models\Permission::create([
            'name' => 'test_permission',
            'guard_name' => 'web'
        ]);
        
        // Assign permission to role
        $role->givePermissionTo($permission);
        
        $this->assertTrue($role->hasPermissionTo($permission));
        $this->assertTrue($role->hasPermissionTo('test_permission'));
    }

    /**
     * Test role assignment to user
     */
    public function test_role_assignment_to_user(): void
    {
        $role = Role::create($this->sampleData);
        $user = \Modules\Auth\Entities\User::factory()->create();
        
        // Assign role to user
        $user->assignRole($role);
        
        $this->assertTrue($user->hasRole($role));
        $this->assertTrue($user->hasRole('test_role'));
    }

    /**
     * Test role deletion with users
     */
    public function test_role_deletion_with_users(): void
    {
        $role = Role::create($this->sampleData);
        $user = \Modules\Auth\Entities\User::factory()->create();
        
        // Assign role to user
        $user->assignRole($role);
        
        // Delete role
        $role->delete();
        
        // Check that role is soft deleted
        $this->assertSoftDeleted('roles', ['id' => $role->id]);
        
        // Check that user no longer has the role
        $this->assertFalse($user->fresh()->hasRole($role));
    }
}
