<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\ReimbursementProcessing;

use Modules\Expenses\Domain\Entities\Reimbursement;

class DefaultReimbursementProcessingStrategy implements ReimbursementProcessingStrategyInterface
{
    public function process(Reimbursement $reimbursement): Reimbursement
    {
        $reimbursement->update(['status' => 'processing']);
        // In a real implementation, this would integrate with a payment gateway
        $reimbursement->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
        return $reimbursement->fresh();
    }
}
