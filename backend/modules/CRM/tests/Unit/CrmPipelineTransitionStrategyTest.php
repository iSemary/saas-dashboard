<?php

namespace Modules\CRM\tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\CRM\Domain\Strategies\PipelineTransition\StrictTransitionStrategy;
use Modules\CRM\Domain\Strategies\PipelineTransition\FlexibleTransitionStrategy;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;
use Modules\CRM\Domain\Entities\Opportunity;

class CrmPipelineTransitionStrategyTest extends TestCase
{
    private Opportunity $opportunity;

    protected function setUp(): void
    {
        $this->opportunity = new Opportunity();
    }

    // ── StrictTransitionStrategy ─────────────────────────────────

    public function test_strict_can_move_to_next_stage(): void
    {
        $strategy = new StrictTransitionStrategy();
        $this->assertTrue($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::PROSPECTING,
            OpportunityStage::QUALIFICATION
        ));
    }

    public function test_strict_cannot_skip_stages(): void
    {
        $strategy = new StrictTransitionStrategy();
        $this->assertFalse($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::PROSPECTING,
            OpportunityStage::NEEDS_ANALYSIS
        ));
    }

    public function test_strict_can_always_move_to_closed_lost(): void
    {
        $strategy = new StrictTransitionStrategy();
        $this->assertTrue($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::PROSPECTING,
            OpportunityStage::CLOSED_LOST
        ));
        $this->assertTrue($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::QUALIFICATION,
            OpportunityStage::CLOSED_LOST
        ));
    }

    public function test_strict_cannot_transition_from_closed_won(): void
    {
        $strategy = new StrictTransitionStrategy();
        $this->assertFalse($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::CLOSED_WON,
            OpportunityStage::PROSPECTING
        ));
    }

    public function test_strict_cannot_transition_from_closed_lost(): void
    {
        $strategy = new StrictTransitionStrategy();
        $this->assertFalse($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::CLOSED_LOST,
            OpportunityStage::PROSPECTING
        ));
    }

    public function test_strict_cannot_move_backwards(): void
    {
        $strategy = new StrictTransitionStrategy();
        $this->assertFalse($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::QUALIFICATION,
            OpportunityStage::PROSPECTING
        ));
    }

    public function test_strict_get_valid_transitions_from_prospecting(): void
    {
        $strategy = new StrictTransitionStrategy();
        $transitions = $strategy->getValidTransitionsFrom(OpportunityStage::PROSPECTING);
        $this->assertCount(2, $transitions);
        $this->assertContains(OpportunityStage::QUALIFICATION, $transitions);
        $this->assertContains(OpportunityStage::CLOSED_LOST, $transitions);
    }

    public function test_strict_no_transitions_from_terminal(): void
    {
        $strategy = new StrictTransitionStrategy();
        $this->assertEmpty($strategy->getValidTransitionsFrom(OpportunityStage::CLOSED_WON));
        $this->assertEmpty($strategy->getValidTransitionsFrom(OpportunityStage::CLOSED_LOST));
    }

    public function test_strict_get_name(): void
    {
        $this->assertSame('Strict Sequential', (new StrictTransitionStrategy())->getName());
    }

    public function test_strict_reports_error_on_invalid_transition(): void
    {
        $strategy = new StrictTransitionStrategy();
        $strategy->canTransition($this->opportunity, OpportunityStage::CLOSED_WON, OpportunityStage::PROSPECTING);
        $this->assertNotNull($strategy->getTransitionError());
    }

    // ── FlexibleTransitionStrategy ───────────────────────────────

    public function test_flexible_can_skip_stages(): void
    {
        $strategy = new FlexibleTransitionStrategy();
        $this->assertTrue($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::PROSPECTING,
            OpportunityStage::NEGOTIATION_REVIEW
        ));
    }

    public function test_flexible_can_always_move_to_closed_lost(): void
    {
        $strategy = new FlexibleTransitionStrategy();
        $this->assertTrue($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::PROSPECTING,
            OpportunityStage::CLOSED_LOST
        ));
    }

    public function test_flexible_cannot_transition_from_terminal(): void
    {
        $strategy = new FlexibleTransitionStrategy();
        $this->assertFalse($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::CLOSED_WON,
            OpportunityStage::PROSPECTING
        ));
        $this->assertFalse($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::CLOSED_LOST,
            OpportunityStage::PROSPECTING
        ));
    }

    public function test_flexible_cannot_move_back_to_prospecting(): void
    {
        $strategy = new FlexibleTransitionStrategy();
        $this->assertFalse($strategy->canTransition(
            $this->opportunity,
            OpportunityStage::QUALIFICATION,
            OpportunityStage::PROSPECTING
        ));
    }

    public function test_flexible_can_stay_at_prospecting(): void
    {
        $strategy = new FlexibleTransitionStrategy();
        // Can move from prospecting to any open stage (except prospecting itself is filtered)
        $transitions = $strategy->getValidTransitionsFrom(OpportunityStage::PROSPECTING);
        $this->assertNotEmpty($transitions);
    }

    public function test_flexible_no_transitions_from_terminal(): void
    {
        $strategy = new FlexibleTransitionStrategy();
        $this->assertEmpty($strategy->getValidTransitionsFrom(OpportunityStage::CLOSED_WON));
    }

    public function test_flexible_get_name(): void
    {
        $this->assertSame('Flexible (Allow Skipping)', (new FlexibleTransitionStrategy())->getName());
    }

    public function test_flexible_reports_error_on_back_to_prospecting(): void
    {
        $strategy = new FlexibleTransitionStrategy();
        $strategy->canTransition($this->opportunity, OpportunityStage::QUALIFICATION, OpportunityStage::PROSPECTING);
        $this->assertNotNull($strategy->getTransitionError());
    }
}
