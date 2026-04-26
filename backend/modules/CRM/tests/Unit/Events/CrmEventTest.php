<?php

namespace Modules\CRM\tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Modules\CRM\Domain\Events\LeadCreated;
use Modules\CRM\Domain\Events\LeadStatusChanged;
use Modules\CRM\Domain\Events\LeadConverted;
use Modules\CRM\Domain\Events\OpportunityStageChanged;
use Modules\CRM\Domain\Events\OpportunityCreated;
use Modules\CRM\Domain\Events\OpportunityClosedWon;
use Modules\CRM\Domain\Events\OpportunityClosedLost;
use Modules\CRM\Domain\Events\CompanyCreated;
use Modules\CRM\Domain\Events\ContactCreated;
use Modules\CRM\Domain\Events\ActivityCreated;
use Modules\CRM\Domain\Events\ActivityCompleted;
use Modules\CRM\Domain\Events\EntityAssigned;

class CrmEventTest extends TestCase
{
    // ── LeadCreated ──────────────────────────────────────────────

    public function test_lead_created_stores_lead_and_data(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $event = new LeadCreated($lead, ['source' => 'website']);

        $this->assertSame($lead, $event->lead());
        $this->assertSame(['source' => 'website'], $event->data());
    }

    public function test_lead_created_default_data_is_empty(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $event = new LeadCreated($lead);

        $this->assertSame([], $event->data());
    }

    // ── LeadStatusChanged ────────────────────────────────────────

    public function test_lead_status_changed_stores_statuses(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $event = new LeadStatusChanged($lead, 'new', 'contacted', 1);

        $this->assertSame($lead, $event->lead);
        $this->assertSame('new', $event->oldStatus);
        $this->assertSame('contacted', $event->newStatus);
        $this->assertSame(1, $event->userId);
    }

    public function test_lead_status_changed_user_id_is_optional(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $event = new LeadStatusChanged($lead, 'new', 'qualified');

        $this->assertNull($event->userId);
    }

    // ── LeadConverted ────────────────────────────────────────────

    public function test_lead_converted_stores_lead_and_opportunity(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $opportunity = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new LeadConverted($lead, $opportunity, 5);

        $this->assertSame($lead, $event->lead);
        $this->assertSame($opportunity, $event->opportunity);
        $this->assertSame(5, $event->userId);
    }

    public function test_lead_converted_user_id_is_optional(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $opportunity = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new LeadConverted($lead, $opportunity);

        $this->assertNull($event->userId);
    }

    // ── OpportunityStageChanged ───────────────────────────────────

    public function test_opportunity_stage_changed_stores_stages(): void
    {
        $opp = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new OpportunityStageChanged($opp, 'prospecting', 'qualification', 2);

        $this->assertSame($opp, $event->opportunity);
        $this->assertSame('prospecting', $event->oldStage);
        $this->assertSame('qualification', $event->newStage);
        $this->assertSame(2, $event->userId);
    }

    // ── OpportunityCreated ────────────────────────────────────────

    public function test_opportunity_created_stores_opportunity(): void
    {
        $opp = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new OpportunityCreated($opp, ['source' => 'lead']);

        $this->assertSame($opp, $event->opportunity);
        $this->assertSame(['source' => 'lead'], $event->data);
    }

    public function test_opportunity_created_default_data_is_empty(): void
    {
        $opp = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new OpportunityCreated($opp);

        $this->assertSame([], $event->data);
    }

    // ── OpportunityClosedWon ──────────────────────────────────────

    public function test_opportunity_closed_won_stores_opportunity_and_revenue(): void
    {
        $opp = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new OpportunityClosedWon($opp, 50000.0, 1);

        $this->assertSame($opp, $event->opportunity);
        $this->assertSame(50000.0, $event->revenue);
        $this->assertSame(1, $event->userId);
    }

    public function test_opportunity_closed_won_user_id_is_optional(): void
    {
        $opp = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new OpportunityClosedWon($opp, 25000.0);

        $this->assertNull($event->userId);
    }

    // ── OpportunityClosedLost ─────────────────────────────────────

    public function test_opportunity_closed_lost_stores_opportunity_and_reason(): void
    {
        $opp = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new OpportunityClosedLost($opp, 'No budget', 2);

        $this->assertSame($opp, $event->opportunity);
        $this->assertSame('No budget', $event->reason);
        $this->assertSame(2, $event->userId);
    }

    public function test_opportunity_closed_lost_reason_and_user_optional(): void
    {
        $opp = $this->createMock(\Modules\CRM\Domain\Entities\Opportunity::class);
        $event = new OpportunityClosedLost($opp);

        $this->assertNull($event->reason);
        $this->assertNull($event->userId);
    }

    // ── CompanyCreated ───────────────────────────────────────────

    public function test_company_created_stores_company(): void
    {
        $company = $this->createMock(\Modules\CRM\Domain\Entities\Company::class);
        $event = new CompanyCreated($company, ['industry' => 'Tech']);

        $this->assertSame($company, $event->company);
        $this->assertSame(['industry' => 'Tech'], $event->data);
    }

    // ── ContactCreated ───────────────────────────────────────────

    public function test_contact_created_stores_contact(): void
    {
        $contact = $this->createMock(\Modules\CRM\Domain\Entities\Contact::class);
        $event = new ContactCreated($contact, ['source' => 'import']);

        $this->assertSame($contact, $event->contact);
        $this->assertSame(['source' => 'import'], $event->data);
    }

    // ── ActivityCreated ──────────────────────────────────────────

    public function test_activity_created_stores_activity(): void
    {
        $activity = $this->createMock(\Modules\CRM\Domain\Entities\Activity::class);
        $event = new ActivityCreated($activity, ['type' => 'call']);

        $this->assertSame($activity, $event->activity);
        $this->assertSame(['type' => 'call'], $event->data);
    }

    // ── ActivityCompleted ────────────────────────────────────────

    public function test_activity_completed_stores_activity_and_outcome(): void
    {
        $activity = $this->createMock(\Modules\CRM\Domain\Entities\Activity::class);
        $event = new ActivityCompleted($activity, 'Interested', 3);

        $this->assertSame($activity, $event->activity);
        $this->assertSame('Interested', $event->outcome);
        $this->assertSame(3, $event->userId);
    }

    public function test_activity_completed_outcome_and_user_optional(): void
    {
        $activity = $this->createMock(\Modules\CRM\Domain\Entities\Activity::class);
        $event = new ActivityCompleted($activity);

        $this->assertNull($event->outcome);
        $this->assertNull($event->userId);
    }

    // ── EntityAssigned ───────────────────────────────────────────

    public function test_entity_assigned_stores_entity_and_users(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $event = new EntityAssigned($lead, 5, 10, 1);

        $this->assertSame($lead, $event->entity);
        $this->assertSame(5, $event->oldUserId);
        $this->assertSame(10, $event->newUserId);
        $this->assertSame(1, $event->assignedBy);
    }

    public function test_entity_assigned_assigned_by_is_optional(): void
    {
        $lead = $this->createMock(\Modules\CRM\Domain\Entities\Lead::class);
        $event = new EntityAssigned($lead, 1, 2);

        $this->assertNull($event->assignedBy);
    }
}
