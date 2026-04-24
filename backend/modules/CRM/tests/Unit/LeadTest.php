<?php

namespace Modules\CRM\Tests\Unit;

use Tests\TestCase;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\Auth\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $lead;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->lead = Lead::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
        ]);
    }

    /**
     * Test lead creation.
     */
    public function test_can_create_lead(): void
    {
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'company' => 'Acme Corp',
            'status' => 'new',
            'source' => 'website',
            'created_by' => $this->user->id,
        ];

        $lead = Lead::create($leadData);

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertEquals('John Doe', $lead->name);
        $this->assertEquals('john@example.com', $lead->email);
        $this->assertEquals('new', $lead->status);
        $this->assertEquals('website', $lead->source);
    }

    /**
     * Test lead relationships.
     */
    public function test_lead_relationships(): void
    {
        $this->assertInstanceOf(User::class, $this->lead->assignedUser);
        $this->assertInstanceOf(User::class, $this->lead->creator);
        $this->assertEquals($this->user->id, $this->lead->assignedUser->id);
        $this->assertEquals($this->user->id, $this->lead->creator->id);
    }

    /**
     * Test lead scopes.
     */
    public function test_lead_scopes(): void
    {
        // Test byStatus scope
        $newLeads = Lead::byStatus('new')->get();
        $this->assertTrue($newLeads->contains($this->lead));

        // Test bySource scope
        $websiteLeads = Lead::bySource('website')->get();
        $this->assertTrue($websiteLeads->contains($this->lead));

        // Test assignedTo scope
        $assignedLeads = Lead::assignedTo($this->user->id)->get();
        $this->assertTrue($assignedLeads->contains($this->lead));
    }

    /**
     * Test lead conversion to opportunity.
     */
    public function test_can_convert_lead_to_opportunity(): void
    {
        $opportunityData = [
            'stage' => 'prospecting',
            'probability' => 25,
        ];

        $opportunity = $this->lead->convertToOpportunity($opportunityData);

        $this->assertInstanceOf(Opportunity::class, $opportunity);
        $this->assertEquals($this->lead->name, $opportunity->name);
        $this->assertEquals($this->lead->id, $opportunity->lead_id);
        $this->assertEquals('prospecting', $opportunity->stage);
        $this->assertEquals(25, $opportunity->probability);

        // Check that lead status is updated
        $this->lead->refresh();
        $this->assertEquals('converted', $this->lead->status);
    }

    /**
     * Test lead fillable attributes.
     */
    public function test_lead_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'email',
            'phone',
            'company',
            'title',
            'description',
            'status',
            'source',
            'expected_revenue',
            'expected_close_date',
            'assigned_to',
            'created_by',
            'custom_fields',
        ];

        $this->assertEquals($fillable, $this->lead->getFillable());
    }

    /**
     * Test lead casts.
     */
    public function test_lead_casts(): void
    {
        $this->lead->update([
            'expected_revenue' => '1000.50',
            'expected_close_date' => '2024-12-31',
            'custom_fields' => ['key' => 'value'],
        ]);

        $this->assertIsFloat($this->lead->expected_revenue);
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->lead->expected_close_date);
        $this->assertIsArray($this->lead->custom_fields);
    }

    /**
     * Test lead soft deletes.
     */
    public function test_lead_soft_deletes(): void
    {
        $leadId = $this->lead->id;
        
        $this->lead->delete();
        
        $this->assertSoftDeleted('leads', ['id' => $leadId]);
        $this->assertNull(Lead::find($leadId));
        $this->assertNotNull(Lead::withTrashed()->find($leadId));
    }
}
