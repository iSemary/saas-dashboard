<?php

namespace Modules\HR\Tests\Unit;

use Modules\HR\Domain\Entities\Application;
use Modules\HR\Domain\Entities\PipelineStage;
use Modules\HR\Domain\Strategies\LinearRecruitmentPipelineStrategy;
use PHPUnit\Framework\TestCase;

class LinearRecruitmentPipelineStrategyTest extends TestCase
{
    public function test_can_advance_only_to_higher_order_stage(): void
    {
        $strategy = new LinearRecruitmentPipelineStrategy();
        $application = new Application();

        $from = new PipelineStage(['order' => 1]);
        $toValid = new PipelineStage(['order' => 2]);
        $toInvalid = new PipelineStage(['order' => 1]);

        $this->assertTrue($strategy->canAdvance($application, $from, $toValid));
        $this->assertFalse($strategy->canAdvance($application, $from, $toInvalid));
    }
}
