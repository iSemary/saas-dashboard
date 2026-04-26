<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\ReimbursementProcessing;

use Modules\Expenses\Domain\Entities\Reimbursement;

interface ReimbursementProcessingStrategyInterface
{
    public function process(Reimbursement $reimbursement): Reimbursement;
}
