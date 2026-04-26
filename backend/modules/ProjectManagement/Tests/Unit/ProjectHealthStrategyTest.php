<?php

namespace Modules\ProjectManagement\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\ProjectManagement\Domain\Strategies\ProjectHealth\DefaultProjectHealthStrategy;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectHealth;

class ProjectHealthStrategyTest extends TestCase
{
    public function test_default_calculates_score_returns_100(): void
    {
        $strategy = new DefaultProjectHealthStrategy();
        $this->assertSame(100.0, $strategy->calculateScore('any-project-id'));
    }

    public function test_default_get_health_label_on_track(): void
    {
        $strategy = new DefaultProjectHealthStrategy();
        $this->assertSame('on_track', $strategy->getHealthLabel(85.0));
    }

    public function test_default_get_health_label_at_risk(): void
    {
        $strategy = new DefaultProjectHealthStrategy();
        $this->assertSame('at_risk', $strategy->getHealthLabel(50.0));
    }

    public function test_default_get_health_label_off_track(): void
    {
        $strategy = new DefaultProjectHealthStrategy();
        $this->assertSame('off_track', $strategy->getHealthLabel(20.0));
    }
}
