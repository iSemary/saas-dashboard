<?php

namespace Modules\Accounting\Tests\Unit;

use Modules\Accounting\Domain\Strategies\JournalValidation\DefaultJournalValidationStrategy;
use Modules\Accounting\Domain\ValueObjects\JournalEntryState;
use PHPUnit\Framework\TestCase;

class DefaultJournalValidationStrategyTest extends TestCase
{
    private DefaultJournalValidationStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new DefaultJournalValidationStrategy();
    }

    public function test_balanced_entry_passes_validation(): void
    {
        $items = [
            ['account_id' => 1, 'debit' => 100.00, 'credit' => 0],
            ['account_id' => 2, 'debit' => 0, 'credit' => 100.00],
        ];

        $result = $this->strategy->validate($items);
        $this->assertTrue($result);
    }

    public function test_unbalanced_entry_fails_validation(): void
    {
        $items = [
            ['account_id' => 1, 'debit' => 100.00, 'credit' => 0],
            ['account_id' => 2, 'debit' => 0, 'credit' => 50.00],
        ];

        $result = $this->strategy->validate($items);
        $this->assertFalse($result);
    }

    public function test_empty_items_fails_validation(): void
    {
        $result = $this->strategy->validate([]);
        $this->assertFalse($result);
    }

    public function test_multi_line_balanced_entry_passes(): void
    {
        $items = [
            ['account_id' => 1, 'debit' => 100.00, 'credit' => 0],
            ['account_id' => 2, 'debit' => 50.00, 'credit' => 0],
            ['account_id' => 3, 'debit' => 0, 'credit' => 150.00],
        ];

        $result = $this->strategy->validate($items);
        $this->assertTrue($result);
    }

    public function test_single_line_entry_fails(): void
    {
        $items = [
            ['account_id' => 1, 'debit' => 100.00, 'credit' => 0],
        ];

        $result = $this->strategy->validate($items);
        $this->assertFalse($result);
    }
}
