<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Base test class for entity testing
 * Provides common assertions and test methods for all entities
 */
abstract class BaseEntityTest extends TestCase
{
    /**
     * The model class being tested
     */
    protected string $modelClass;

    /**
     * The table name for the model
     */
    protected string $tableName;

    /**
     * Expected fillable attributes
     */
    protected array $expectedFillable = [];

    /**
     * Expected hidden attributes
     */
    protected array $expectedHidden = [];

    /**
     * Expected casts
     */
    protected array $expectedCasts = [];

    /**
     * Expected traits
     */
    protected array $expectedTraits = [];

    /**
     * Expected interfaces
     */
    protected array $expectedInterfaces = [];

    /**
     * Sample data for testing
     */
    protected array $sampleData = [];

    /**
     * Test that the model can be instantiated
     */
    public function test_can_be_instantiated(): void
    {
        $model = new $this->modelClass();
        $this->assertInstanceOf($this->modelClass, $model);
    }

    /**
     * Test that the model has correct fillable attributes
     */
    public function test_has_correct_fillable_attributes(): void
    {
        if (!empty($this->expectedFillable)) {
            $model = new $this->modelClass();
            $this->assertModelHasFillable($model, $this->expectedFillable);
        }
    }

    /**
     * Test that the model has correct hidden attributes
     */
    public function test_has_correct_hidden_attributes(): void
    {
        if (!empty($this->expectedHidden)) {
            $model = new $this->modelClass();
            $this->assertModelHasHidden($model, $this->expectedHidden);
        }
    }

    /**
     * Test that the model has correct casts
     */
    public function test_has_correct_casts(): void
    {
        if (!empty($this->expectedCasts)) {
            $model = new $this->modelClass();
            $this->assertModelHasCasts($model, $this->expectedCasts);
        }
    }

    /**
     * Test that the model uses expected traits
     */
    public function test_uses_expected_traits(): void
    {
        if (!empty($this->expectedTraits)) {
            $model = new $this->modelClass();
            $this->assertModelUsesTraits($model, $this->expectedTraits);
        }
    }

    /**
     * Test that the model implements expected interfaces
     */
    public function test_implements_expected_interfaces(): void
    {
        if (!empty($this->expectedInterfaces)) {
            $model = new $this->modelClass();
            $this->assertModelImplementsInterfaces($model, $this->expectedInterfaces);
        }
    }

    /**
     * Test that the model can be created with sample data
     */
    public function test_can_be_created_with_sample_data(): void
    {
        if (!empty($this->sampleData)) {
            $model = $this->modelClass::create($this->sampleData);
            $this->assertInstanceOf($this->modelClass, $model);
            $this->assertDatabaseHas($this->tableName, $this->sampleData);
        }
    }

    /**
     * Test that the model can be updated
     */
    public function test_can_be_updated(): void
    {
        if (!empty($this->sampleData)) {
            $model = $this->modelClass::create($this->sampleData);
            $updateData = ['name' => 'Updated Name'];
            
            $model->update($updateData);
            $this->assertDatabaseHas($this->tableName, $updateData);
        }
    }

    /**
     * Test that the model can be deleted (soft delete if applicable)
     */
    public function test_can_be_deleted(): void
    {
        if (!empty($this->sampleData)) {
            $model = $this->modelClass::create($this->sampleData);
            $modelId = $model->id;
            
            $model->delete();
            
            // Check if model uses soft deletes
            if (in_array(SoftDeletes::class, $this->expectedTraits)) {
                $this->assertSoftDeleted($this->tableName, ['id' => $modelId]);
            } else {
                $this->assertDatabaseMissing($this->tableName, ['id' => $modelId]);
            }
        }
    }

    /**
     * Test that the model can be restored (if soft deletes are used)
     */
    public function test_can_be_restored(): void
    {
        if (!empty($this->sampleData) && in_array(SoftDeletes::class, $this->expectedTraits)) {
            $model = $this->modelClass::create($this->sampleData);
            $modelId = $model->id;
            
            $model->delete();
            $model->restore();
            
            $this->assertDatabaseHas($this->tableName, ['id' => $modelId, 'deleted_at' => null]);
        }
    }

    /**
     * Test that the model has a factory (if HasFactory trait is used)
     */
    public function test_has_factory(): void
    {
        if (in_array(HasFactory::class, $this->expectedTraits)) {
            $this->assertTrue(method_exists($this->modelClass, 'factory'));
        }
    }

    /**
     * Test that the model can be created using factory
     */
    public function test_can_be_created_using_factory(): void
    {
        if (in_array(HasFactory::class, $this->expectedTraits)) {
            $model = $this->modelClass::factory()->create();
            $this->assertInstanceOf($this->modelClass, $model);
            $this->assertDatabaseHas($this->tableName, ['id' => $model->id]);
        }
    }

    /**
     * Test that the model has auditing (if Auditable interface is implemented)
     */
    public function test_has_auditing(): void
    {
        if (in_array(Auditable::class, $this->expectedInterfaces)) {
            $model = new $this->modelClass();
            $this->assertInstanceOf(Auditable::class, $model);
        }
    }

    /**
     * Test model relationships (to be implemented by child classes)
     */
    public function test_relationships(): void
    {
        // This method should be overridden by child classes to test specific relationships
        $this->assertTrue(true);
    }

    /**
     * Test model scopes (to be implemented by child classes)
     */
    public function test_scopes(): void
    {
        // This method should be overridden by child classes to test specific scopes
        $this->assertTrue(true);
    }

    /**
     * Test model accessors and mutators (to be implemented by child classes)
     */
    public function test_accessors_and_mutators(): void
    {
        // This method should be overridden by child classes to test specific accessors/mutators
        $this->assertTrue(true);
    }
}
