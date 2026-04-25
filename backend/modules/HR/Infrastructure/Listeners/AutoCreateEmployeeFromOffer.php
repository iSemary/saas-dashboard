<?php

namespace Modules\HR\Infrastructure\Listeners;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Domain\Events\OfferAccepted;

class AutoCreateEmployeeFromOffer
{
    public function handle(OfferAccepted $event): void
    {
        $offer = $event->offer->loadMissing(['candidate', 'jobOpening']);
        $candidate = $offer->candidate;
        $jobOpening = $offer->jobOpening;

        if (!$candidate || !$jobOpening) {
            return;
        }

        if (Employee::query()->where('email', $candidate->email)->exists()) {
            return;
        }

        Employee::create([
            'employee_number' => 'EMP-' . now()->format('Ymd') . '-' . str_pad((string) (Employee::count() + 1), 5, '0', STR_PAD_LEFT),
            'first_name' => $candidate->first_name,
            'last_name' => $candidate->last_name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'department_id' => $jobOpening->department_id,
            'position_id' => $jobOpening->position_id,
            'hire_date' => $offer->start_date,
            'salary' => $offer->salary,
            'currency' => $offer->currency ?? 'USD',
            'employment_type' => $jobOpening->employment_type ?? 'full_time',
            'employment_status' => 'active',
            'created_by' => $offer->created_by,
        ]);
    }
}
