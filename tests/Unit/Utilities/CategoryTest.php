<?php

namespace Tests\Unit\Utilities;

use Tests\Unit\BaseEntityTest;
use Modules\Utilities\Entities\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class CategoryTest extends BaseEntityTest
{
    protected string $modelClass = Category::class;
    protected string $tableName = 'categories';

    protected array $expectedFillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
        'priority',
        'status',
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
        'name' => 'Test Category',
        'slug' => 'test-category',
        'description' => 'Test category description',
        'priority' => 1,
        'status' => 'active',
    ];

    /**
     * Test that the model has correct connection
     */
    public function test_has_correct_connection(): void
    {
        $model = new Category();
        $this->assertEquals('landlord', $model->getConnectionName());
    }

    /**
     * Test that the model has correct titles
     */
    public function test_has_correct_titles(): void
    {
        $model = new Category();
        $this->assertEquals('category', $model->singleTitle);
        $this->assertEquals('categories', $model->pluralTitle);
    }

    /**
     * Test that the model can be created with all required fields
     */
    public function test_can_be_created_with_all_required_fields(): void
    {
        $category = Category::create($this->sampleData);
        
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($this->sampleData['name'], $category->name);
        $this->assertEquals($this->sampleData['slug'], $category->slug);
        $this->assertEquals($this->sampleData['description'], $category->description);
        $this->assertEquals($this->sampleData['priority'], $category->priority);
        $this->assertEquals($this->sampleData['status'], $category->status);
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        $category = Category::create($this->sampleData);
        
        $updateData = [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'priority' => 2,
            'status' => 'inactive',
        ];
        
        $category->update($updateData);
        
        $this->assertEquals($updateData['name'], $category->fresh()->name);
        $this->assertEquals($updateData['slug'], $category->fresh()->slug);
        $this->assertEquals($updateData['priority'], $category->fresh()->priority);
        $this->assertEquals($updateData['status'], $category->fresh()->status);
    }

    /**
     * Test that the model can be soft deleted
     */
    public function test_can_be_soft_deleted(): void
    {
        $category = Category::create($this->sampleData);
        $categoryId = $category->id;
        
        $category->delete();
        
        $this->assertSoftDeleted('categories', ['id' => $categoryId]);
        $this->assertNull(Category::find($categoryId));
        $this->assertNotNull(Category::withTrashed()->find($categoryId));
    }

    /**
     * Test that the model can be restored
     */
    public function test_can_be_restored(): void
    {
        $category = Category::create($this->sampleData);
        $categoryId = $category->id;
        
        $category->delete();
        $category->restore();
        
        $this->assertDatabaseHas('categories', [
            'id' => $categoryId,
            'deleted_at' => null
        ]);
        $this->assertNotNull(Category::find($categoryId));
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        $category = Category::factory()->create();
        
        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /**
     * Test that the model has auditing enabled
     */
    public function test_has_auditing_enabled(): void
    {
        $category = Category::create($this->sampleData);
        
        // Update the model to trigger auditing
        $category->update(['name' => 'Updated Name']);
        
        // Check if audit record was created
        $this->assertDatabaseHas('audits', [
            'auditable_type' => Category::class,
            'auditable_id' => $category->id,
            'event' => 'updated'
        ]);
    }

    /**
     * Test translatable columns
     */
    public function test_translatable_columns(): void
    {
        $category = new Category();
        $expectedTranslatableColumns = ['name', 'description'];
        
        $this->assertEquals($expectedTranslatableColumns, $category->translatableColumns);
    }

    /**
     * Test file columns configuration
     */
    public function test_file_columns_configuration(): void
    {
        $category = new Category();
        $expectedFileColumns = [
            'icon' => [
                'folder' => 'categories',
                'is_encrypted' => false,
                'access_level' => 'public',
                'metadata' => ['width', 'height', 'aspect_ratio'],
            ],
        ];
        
        $this->assertEquals($expectedFileColumns, $category->fileColumns);
    }

    /**
     * Test parent-child relationship
     */
    public function test_parent_child_relationship(): void
    {
        $parentCategory = Category::create($this->sampleData);
        
        $childCategory = Category::create(array_merge($this->sampleData, [
            'name' => 'Child Category',
            'slug' => 'child-category',
            'parent_id' => $parentCategory->id,
        ]));
        
        $this->assertEquals($parentCategory->id, $childCategory->parent_id);
    }

    /**
     * Test status enum values
     */
    public function test_status_enum_values(): void
    {
        $activeCategory = Category::create(array_merge($this->sampleData, ['status' => 'active']));
        $inactiveCategory = Category::create(array_merge($this->sampleData, ['status' => 'inactive']));
        
        $this->assertEquals('active', $activeCategory->status);
        $this->assertEquals('inactive', $inactiveCategory->status);
    }

    /**
     * Test priority ordering
     */
    public function test_priority_ordering(): void
    {
        $category1 = Category::create(array_merge($this->sampleData, [
            'name' => 'Category 1',
            'slug' => 'category-1',
            'priority' => 1,
        ]));
        
        $category2 = Category::create(array_merge($this->sampleData, [
            'name' => 'Category 2',
            'slug' => 'category-2',
            'priority' => 2,
        ]));
        
        $this->assertEquals(1, $category1->priority);
        $this->assertEquals(2, $category2->priority);
    }

    /**
     * Test slug generation
     */
    public function test_slug_generation(): void
    {
        $category = Category::create(array_merge($this->sampleData, [
            'name' => 'Test Category Name',
            'slug' => 'test-category-name',
        ]));
        
        $this->assertEquals('test-category-name', $category->slug);
    }

    /**
     * Test icon attribute accessor
     */
    public function test_icon_attribute_accessor(): void
    {
        $category = new Category();
        
        // Test that getIconAttribute method exists
        $this->assertTrue(method_exists($category, 'getIconAttribute'));
    }

    /**
     * Test icon attribute mutator
     */
    public function test_icon_attribute_mutator(): void
    {
        $category = new Category();
        
        // Test that setIconAttribute method exists
        $this->assertTrue(method_exists($category, 'setIconAttribute'));
    }

    /**
     * Test unique slug constraint
     */
    public function test_unique_slug_constraint(): void
    {
        Category::create($this->sampleData);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Category::create(array_merge($this->sampleData, [
            'name' => 'Different Name'
        ]));
    }

    /**
     * Test category hierarchy
     */
    public function test_category_hierarchy(): void
    {
        $parent = Category::create($this->sampleData);
        $child = Category::create(array_merge($this->sampleData, [
            'name' => 'Child Category',
            'slug' => 'child-category',
            'parent_id' => $parent->id,
        ]));
        
        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertTrue($child->parent_id > 0);
    }

    /**
     * Test category with icon
     */
    public function test_category_with_icon(): void
    {
        $category = Category::create(array_merge($this->sampleData, [
            'icon' => 'test-icon.png',
        ]));
        
        $this->assertEquals('test-icon.png', $category->icon);
    }
}
