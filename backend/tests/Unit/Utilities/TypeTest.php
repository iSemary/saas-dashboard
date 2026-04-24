<?php

namespace Tests\Unit\Utilities;

use Tests\Unit\BaseEntityTest;
use Modules\Utilities\Entities\Type;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class TypeTest extends BaseEntityTest
{
    protected string $modelClass = Type::class;
    protected string $tableName = 'types';

    protected array $expectedFillable = [
        'name',
        'slug',
        'description',
        'status',
        'icon',
        'priority',
    ];

    protected array $expectedTraits = [
        HasFactory::class,
        SoftDeletes::class,
        \OwenIt\Auditing\Auditable::class,
        \Modules\FileManager\Traits\FileHandler::class,
        \Modules\Localization\Traits\Translatable::class,
    ];

    protected array $expectedInterfaces = [
        Auditable::class,
    ];

    protected array $sampleData = [
        'name' => 'Test Type',
        'slug' => 'test-type',
        'description' => 'Test type description',
        'status' => 'active',
        'priority' => 1,
    ];

    /**
     * Test that the model has correct connection
     */
    public function test_has_correct_connection(): void
    {
        $model = new Type();
        $this->assertEquals('landlord', $model->getConnectionName());
    }

    /**
     * Test that the model has correct titles
     */
    public function test_has_correct_titles(): void
    {
        $model = new Type();
        $this->assertEquals('type', $model->singleTitle);
        $this->assertEquals('types', $model->pluralTitle);
    }

    /**
     * Test that the model can be created with all required fields
     */
    public function test_can_be_created_with_all_required_fields(): void
    {
        $type = Type::create($this->sampleData);
        
        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals($this->sampleData['name'], $type->name);
        $this->assertEquals($this->sampleData['slug'], $type->slug);
        $this->assertEquals($this->sampleData['description'], $type->description);
        $this->assertEquals($this->sampleData['status'], $type->status);
        $this->assertEquals($this->sampleData['priority'], $type->priority);
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        $type = Type::create($this->sampleData);
        
        $updateData = [
            'name' => 'Updated Type',
            'slug' => 'updated-type',
            'priority' => 2,
            'status' => 'inactive',
        ];
        
        $type->update($updateData);
        
        $this->assertEquals($updateData['name'], $type->fresh()->name);
        $this->assertEquals($updateData['slug'], $type->fresh()->slug);
        $this->assertEquals($updateData['priority'], $type->fresh()->priority);
        $this->assertEquals($updateData['status'], $type->fresh()->status);
    }

    /**
     * Test that the model can be soft deleted
     */
    public function test_can_be_soft_deleted(): void
    {
        $type = Type::create($this->sampleData);
        $typeId = $type->id;
        
        $type->delete();
        
        $this->assertSoftDeleted('types', ['id' => $typeId]);
        $this->assertNull(Type::find($typeId));
        $this->assertNotNull(Type::withTrashed()->find($typeId));
    }

    /**
     * Test that the model can be restored
     */
    public function test_can_be_restored(): void
    {
        $type = Type::create($this->sampleData);
        $typeId = $type->id;
        
        $type->delete();
        $type->restore();
        
        $this->assertDatabaseHas('types', [
            'id' => $typeId,
            'deleted_at' => null
        ]);
        $this->assertNotNull(Type::find($typeId));
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        $type = Type::factory()->create();
        
        $this->assertInstanceOf(Type::class, $type);
        $this->assertDatabaseHas('types', ['id' => $type->id]);
    }

    /**
     * Test that the model has auditing enabled
     */
    public function test_has_auditing_enabled(): void
    {
        $type = Type::create($this->sampleData);
        
        // Update the model to trigger auditing
        $type->update(['name' => 'Updated Name']);
        
        // Check if audit record was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => Type::class,
            'auditable_id' => $type->id,
            'event' => 'updated'
        ]);
    }

    /**
     * Test translatable columns
     */
    public function test_translatable_columns(): void
    {
        $type = new Type();
        $expectedTranslatableColumns = ['name', 'description'];
        
        $this->assertEquals($expectedTranslatableColumns, $type->translatableColumns);
    }

    /**
     * Test file columns configuration
     */
    public function test_file_columns_configuration(): void
    {
        $type = new Type();
        $expectedFileColumns = [
            'icon' => [
                'folder' => 'types',
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => ['width', 'height', 'aspect_ratio'],
            ],
        ];
        
        $this->assertEquals($expectedFileColumns, $type->fileColumns);
    }

    /**
     * Test status enum values
     */
    public function test_status_enum_values(): void
    {
        $activeType = Type::create(array_merge($this->sampleData, ['status' => 'active']));
        $inactiveType = Type::create(array_merge($this->sampleData, ['status' => 'inactive']));
        
        $this->assertEquals('active', $activeType->status);
        $this->assertEquals('inactive', $inactiveType->status);
    }

    /**
     * Test priority ordering
     */
    public function test_priority_ordering(): void
    {
        $type1 = Type::create(array_merge($this->sampleData, [
            'name' => 'Type 1',
            'slug' => 'type-1',
            'priority' => 1,
        ]));
        
        $type2 = Type::create(array_merge($this->sampleData, [
            'name' => 'Type 2',
            'slug' => 'type-2',
            'priority' => 2,
        ]));
        
        $this->assertEquals(1, $type1->priority);
        $this->assertEquals(2, $type2->priority);
    }

    /**
     * Test slug generation
     */
    public function test_slug_generation(): void
    {
        $type = Type::create(array_merge($this->sampleData, [
            'name' => 'Test Type Name',
            'slug' => 'test-type-name',
        ]));
        
        $this->assertEquals('test-type-name', $type->slug);
    }

    /**
     * Test icon attribute accessor
     */
    public function test_icon_attribute_accessor(): void
    {
        $type = new Type();
        
        // Test that getIconAttribute method exists
        $this->assertTrue(method_exists($type, 'getIconAttribute'));
    }

    /**
     * Test icon attribute mutator
     */
    public function test_icon_attribute_mutator(): void
    {
        $type = new Type();
        
        // Test that setIconAttribute method exists
        $this->assertTrue(method_exists($type, 'setIconAttribute'));
    }

    /**
     * Test unique slug constraint
     */
    public function test_unique_slug_constraint(): void
    {
        Type::create($this->sampleData);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Type::create(array_merge($this->sampleData, [
            'name' => 'Different Name'
        ]));
    }

    /**
     * Test type with icon
     */
    public function test_type_with_icon(): void
    {
        $type = Type::create(array_merge($this->sampleData, [
            'icon' => 'test-icon.png',
        ]));
        
        $this->assertEquals('test-icon.png', $type->icon);
    }

    /**
     * Test type without icon
     */
    public function test_type_without_icon(): void
    {
        $type = Type::create($this->sampleData);
        
        $this->assertNull($type->icon);
    }

    /**
     * Test type with long description
     */
    public function test_type_with_long_description(): void
    {
        $longDescription = str_repeat('This is a long description for testing purposes. ', 20);
        
        $type = Type::create(array_merge($this->sampleData, [
            'description' => $longDescription,
        ]));
        
        $this->assertEquals($longDescription, $type->description);
    }
}
