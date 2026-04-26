<?php

namespace Modules\CRM\tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\CRM\Domain\ValueObjects\LeadStatus;
use Modules\CRM\Domain\ValueObjects\LeadSource;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;
use Modules\CRM\Domain\ValueObjects\ActivityStatus;
use Modules\CRM\Domain\ValueObjects\ActivityType;
use Modules\CRM\Domain\ValueObjects\CompanyType;
use Modules\CRM\Domain\ValueObjects\Money;
use Modules\CRM\Domain\ValueObjects\Address;

class CrmValueObjectTest extends TestCase
{
    // ── LeadStatus ────────────────────────────────────────────────

    public function test_lead_status_values(): void
    {
        $this->assertSame('new', LeadStatus::NEW->value);
        $this->assertSame('contacted', LeadStatus::CONTACTED->value);
        $this->assertSame('qualified', LeadStatus::QUALIFIED->value);
        $this->assertSame('unqualified', LeadStatus::UNQUALIFIED->value);
        $this->assertSame('converted', LeadStatus::CONVERTED->value);
    }

    public function test_lead_status_all_cases_covered(): void
    {
        $this->assertCount(5, LeadStatus::cases());
    }

    public function test_lead_status_label(): void
    {
        $this->assertSame('New', LeadStatus::NEW->label());
        $this->assertSame('Contacted', LeadStatus::CONTACTED->label());
        $this->assertSame('Qualified', LeadStatus::QUALIFIED->label());
        $this->assertSame('Unqualified', LeadStatus::UNQUALIFIED->label());
        $this->assertSame('Converted', LeadStatus::CONVERTED->label());
    }

    public function test_lead_status_valid_transitions(): void
    {
        $this->assertTrue(LeadStatus::canTransitionFrom(LeadStatus::NEW, LeadStatus::CONTACTED));
        $this->assertTrue(LeadStatus::canTransitionFrom(LeadStatus::NEW, LeadStatus::UNQUALIFIED));
        $this->assertTrue(LeadStatus::canTransitionFrom(LeadStatus::CONTACTED, LeadStatus::QUALIFIED));
        $this->assertTrue(LeadStatus::canTransitionFrom(LeadStatus::QUALIFIED, LeadStatus::CONVERTED));
        $this->assertTrue(LeadStatus::canTransitionFrom(LeadStatus::UNQUALIFIED, LeadStatus::NEW));
    }

    public function test_lead_status_invalid_transitions(): void
    {
        $this->assertFalse(LeadStatus::canTransitionFrom(LeadStatus::CONVERTED, LeadStatus::NEW));
        $this->assertFalse(LeadStatus::canTransitionFrom(LeadStatus::CONVERTED, LeadStatus::CONTACTED));
    }

    public function test_lead_status_from_string_invalid_defaults(): void
    {
        $this->assertSame(LeadStatus::NEW, LeadStatus::fromString('nonexistent'));
    }

    public function test_lead_status_all_returns_array(): void
    {
        $all = LeadStatus::all();
        $this->assertCount(5, $all);
        $this->assertArrayHasKey('value', $all[0]);
        $this->assertArrayHasKey('label', $all[0]);
        $this->assertArrayHasKey('color', $all[0]);
    }

    // ── LeadSource ────────────────────────────────────────────────

    public function test_lead_source_values(): void
    {
        $this->assertSame('website', LeadSource::WEBSITE->value);
        $this->assertSame('phone', LeadSource::PHONE->value);
        $this->assertSame('email', LeadSource::EMAIL->value);
        $this->assertSame('social', LeadSource::SOCIAL->value);
        $this->assertSame('referral', LeadSource::REFERRAL->value);
        $this->assertSame('advertisement', LeadSource::ADVERTISEMENT->value);
        $this->assertSame('trade_show', LeadSource::TRADE_SHOW->value);
        $this->assertSame('partner', LeadSource::PARTNER->value);
        $this->assertSame('other', LeadSource::OTHER->value);
    }

    public function test_lead_source_all_cases_covered(): void
    {
        $this->assertCount(9, LeadSource::cases());
    }

    public function test_lead_source_from_string_invalid_defaults(): void
    {
        $this->assertSame(LeadSource::OTHER, LeadSource::fromString('nonexistent'));
    }

    // ── OpportunityStage ──────────────────────────────────────────

    public function test_opportunity_stage_values(): void
    {
        $this->assertSame('prospecting', OpportunityStage::PROSPECTING->value);
        $this->assertSame('closed_won', OpportunityStage::CLOSED_WON->value);
        $this->assertSame('closed_lost', OpportunityStage::CLOSED_LOST->value);
    }

    public function test_opportunity_stage_all_cases_covered(): void
    {
        $this->assertCount(10, OpportunityStage::cases());
    }

    public function test_opportunity_stage_probability(): void
    {
        $this->assertSame(10, OpportunityStage::PROSPECTING->probability());
        $this->assertSame(100, OpportunityStage::CLOSED_WON->probability());
        $this->assertSame(0, OpportunityStage::CLOSED_LOST->probability());
        $this->assertSame(80, OpportunityStage::NEGOTIATION_REVIEW->probability());
    }

    public function test_opportunity_stage_is_terminal(): void
    {
        $this->assertTrue(OpportunityStage::CLOSED_WON->isTerminal());
        $this->assertTrue(OpportunityStage::CLOSED_LOST->isTerminal());
        $this->assertFalse(OpportunityStage::PROSPECTING->isTerminal());
    }

    public function test_opportunity_stage_is_open(): void
    {
        $this->assertTrue(OpportunityStage::PROSPECTING->isOpen());
        $this->assertFalse(OpportunityStage::CLOSED_WON->isOpen());
    }

    public function test_opportunity_stage_valid_transitions(): void
    {
        $this->assertTrue(OpportunityStage::canTransitionFrom(OpportunityStage::PROSPECTING, OpportunityStage::CLOSED_WON));
        $this->assertTrue(OpportunityStage::canTransitionFrom(OpportunityStage::QUALIFICATION, OpportunityStage::NEGOTIATION_REVIEW));
    }

    public function test_opportunity_stage_terminal_cannot_transition(): void
    {
        $this->assertFalse(OpportunityStage::canTransitionFrom(OpportunityStage::CLOSED_WON, OpportunityStage::PROSPECTING));
        $this->assertFalse(OpportunityStage::canTransitionFrom(OpportunityStage::CLOSED_LOST, OpportunityStage::PROSPECTING));
    }

    public function test_opportunity_stage_open_stages_excludes_terminal(): void
    {
        $openStages = OpportunityStage::openStages();
        $this->assertCount(8, $openStages);
    }

    // ── ActivityStatus ────────────────────────────────────────────

    public function test_activity_status_values(): void
    {
        $this->assertSame('planned', ActivityStatus::PLANNED->value);
        $this->assertSame('in_progress', ActivityStatus::IN_PROGRESS->value);
        $this->assertSame('completed', ActivityStatus::COMPLETED->value);
        $this->assertSame('cancelled', ActivityStatus::CANCELLED->value);
        $this->assertSame('overdue', ActivityStatus::OVERDUE->value);
    }

    public function test_activity_status_is_terminal(): void
    {
        $this->assertTrue(ActivityStatus::COMPLETED->isTerminal());
        $this->assertTrue(ActivityStatus::CANCELLED->isTerminal());
        $this->assertFalse(ActivityStatus::PLANNED->isTerminal());
    }

    public function test_activity_status_can_transition_to(): void
    {
        $this->assertTrue(ActivityStatus::PLANNED->canTransitionTo(ActivityStatus::IN_PROGRESS));
        $this->assertFalse(ActivityStatus::COMPLETED->canTransitionTo(ActivityStatus::PLANNED));
        $this->assertFalse(ActivityStatus::CANCELLED->canTransitionTo(ActivityStatus::IN_PROGRESS));
    }

    // ── ActivityType ──────────────────────────────────────────────

    public function test_activity_type_values(): void
    {
        $this->assertSame('call', ActivityType::CALL->value);
        $this->assertSame('email', ActivityType::EMAIL->value);
        $this->assertSame('meeting', ActivityType::MEETING->value);
        $this->assertSame('task', ActivityType::TASK->value);
        $this->assertSame('note', ActivityType::NOTE->value);
    }

    public function test_activity_type_all_cases_covered(): void
    {
        $this->assertCount(9, ActivityType::cases());
    }

    public function test_activity_type_requires_outcome(): void
    {
        $this->assertTrue(ActivityType::CALL->requiresOutcome());
        $this->assertTrue(ActivityType::EMAIL->requiresOutcome());
        $this->assertTrue(ActivityType::MEETING->requiresOutcome());
        $this->assertTrue(ActivityType::DEMO->requiresOutcome());
        $this->assertFalse(ActivityType::NOTE->requiresOutcome());
        $this->assertFalse(ActivityType::TASK->requiresOutcome());
    }

    // ── CompanyType ───────────────────────────────────────────────

    public function test_company_type_values(): void
    {
        $this->assertSame('customer', CompanyType::CUSTOMER->value);
        $this->assertSame('prospect', CompanyType::PROSPECT->value);
        $this->assertSame('partner', CompanyType::PARTNER->value);
        $this->assertSame('vendor', CompanyType::VENDOR->value);
        $this->assertSame('competitor', CompanyType::COMPETITOR->value);
        $this->assertSame('other', CompanyType::OTHER->value);
    }

    public function test_company_type_can_have_parent(): void
    {
        $this->assertTrue(CompanyType::CUSTOMER->canHaveParent());
        $this->assertTrue(CompanyType::PROSPECT->canHaveParent());
        $this->assertFalse(CompanyType::PARTNER->canHaveParent());
        $this->assertFalse(CompanyType::VENDOR->canHaveParent());
    }

    // ── Money ──────────────────────────────────────────────────────

    public function test_money_construction(): void
    {
        $money = new Money(100.0);
        $this->assertSame(100.0, $money->amount());
        $this->assertSame('USD', $money->currency());
    }

    public function test_money_rejects_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Money(-1.0);
    }

    public function test_money_add(): void
    {
        $a = new Money(50.0);
        $b = new Money(30.0);
        $result = $a->add($b);
        $this->assertSame(80.0, $result->amount());
    }

    public function test_money_subtract(): void
    {
        $a = new Money(100.0);
        $b = new Money(40.0);
        $result = $a->subtract($b);
        $this->assertSame(60.0, $result->amount());
    }

    public function test_money_subtract_rejects_negative_result(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $a = new Money(10.0);
        $b = new Money(50.0);
        $a->subtract($b);
    }

    public function test_money_multiply(): void
    {
        $money = new Money(100.0);
        $result = $money->multiply(1.5);
        $this->assertSame(150.0, $result->amount());
    }

    public function test_money_multiply_rejects_negative_factor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $money = new Money(100.0);
        $money->multiply(-0.5);
    }

    public function test_money_different_currency_cannot_operate(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $usd = new Money(100.0, 'USD');
        $eur = new Money(50.0, 'EUR');
        $usd->add($eur);
    }

    public function test_money_is_zero(): void
    {
        $this->assertTrue(Money::zero()->isZero());
        $this->assertFalse((new Money(1.0))->isZero());
    }

    public function test_money_equals(): void
    {
        $a = new Money(100.0);
        $b = new Money(100.0);
        $this->assertTrue($a->equals($b));
    }

    public function test_money_formatted(): void
    {
        $money = new Money(1234.56);
        $this->assertSame('$1,234.56', $money->formatted());
    }

    // ── Address ───────────────────────────────────────────────────

    public function test_address_construction(): void
    {
        $addr = new Address('123 Main St', 'Springfield', 'IL', '62701', 'US', 'US');
        $this->assertSame('123 Main St', $addr->street());
        $this->assertSame('Springfield', $addr->city());
    }

    public function test_address_from_array(): void
    {
        $addr = Address::fromArray([
            'street' => '456 Oak Ave',
            'city' => 'Portland',
            'state' => 'OR',
            'postal_code' => '97201',
            'country' => 'US',
        ]);
        $this->assertSame('456 Oak Ave', $addr->street());
        $this->assertSame('97201', $addr->postalCode());
    }

    public function test_address_formatted(): void
    {
        $addr = new Address('123 Main St', 'Springfield', 'IL', '62701', 'US');
        $this->assertSame('123 Main St, Springfield, IL, 62701, US', $addr->formatted());
    }

    public function test_address_is_empty(): void
    {
        $this->assertTrue((new Address())->isEmpty());
        $this->assertFalse((new Address(street: '123 Main St'))->isEmpty());
    }

    public function test_address_with_street_returns_new_instance(): void
    {
        $original = new Address('Old St', 'City');
        $updated = $original->withStreet('New St');
        $this->assertSame('Old St', $original->street());
        $this->assertSame('New St', $updated->street());
    }

    public function test_address_to_array(): void
    {
        $addr = new Address('123 Main St', 'Springfield');
        $arr = $addr->toArray();
        $this->assertArrayHasKey('street', $arr);
        $this->assertArrayHasKey('city', $arr);
        $this->assertArrayHasKey('postal_code', $arr);
        $this->assertArrayHasKey('formatted', $arr);
    }
}
