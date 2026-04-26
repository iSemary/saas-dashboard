<?php

namespace Modules\Accounting\Tests\Unit;

use Modules\Accounting\Domain\ValueObjects\JournalEntryState;
use PHPUnit\Framework\TestCase;

class JournalEntryStateMachineTest extends TestCase
{
    public function test_valid_transitions_from_draft(): void
    {
        $this->assertTrue(JournalEntryState::canTransitionFrom('draft', JournalEntryState::POSTED));
        $this->assertTrue(JournalEntryState::canTransitionFrom('draft', JournalEntryState::CANCELLED));
    }

    public function test_invalid_transition_from_posted_to_draft(): void
    {
        $this->assertFalse(JournalEntryState::canTransitionFrom('posted', JournalEntryState::DRAFT));
    }

    public function test_posted_can_transition_to_cancelled(): void
    {
        $this->assertTrue(JournalEntryState::canTransitionFrom('posted', JournalEntryState::CANCELLED));
    }

    public function test_cancelled_cannot_transition_to_anything(): void
    {
        $this->assertFalse(JournalEntryState::canTransitionFrom('cancelled', JournalEntryState::DRAFT));
        $this->assertFalse(JournalEntryState::canTransitionFrom('cancelled', JournalEntryState::POSTED));
    }

    public function test_draft_is_editable(): void
    {
        $this->assertTrue(JournalEntryState::DRAFT->isEditable());
    }

    public function test_posted_is_not_editable(): void
    {
        $this->assertFalse(JournalEntryState::POSTED->isEditable());
    }

    public function test_cancelled_is_not_editable(): void
    {
        $this->assertFalse(JournalEntryState::CANCELLED->isEditable());
    }
}
