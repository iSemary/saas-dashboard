<?php

namespace Modules\CRM\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\Models\Contact;
use OwenIt\Auditing\Models\Audit;

class ContactRepository implements ContactRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Contact::with(['company', 'assignedUser', 'creator']);

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['company_id']) && $filters['company_id']) {
            $query->where('company_id', $filters['company_id']);
        }

        if (isset($filters['assigned_to']) && $filters['assigned_to']) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['brand_id']) && $filters['brand_id']) {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Contact
    {
        return Contact::with(['company', 'assignedUser', 'creator', 'opportunities'])->findOrFail($id);
    }

    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    public function update(int $id, array $data): Contact
    {
        $contact = Contact::findOrFail($id);
        $contact->update($data);
        return $contact->load(['company', 'assignedUser', 'creator']);
    }

    public function delete(int $id): bool
    {
        return Contact::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Contact::whereIn('id', $ids)->delete();
    }

    public function getActivity(int $id, int $perPage = 20): LengthAwarePaginator
    {
        return Audit::where('auditable_type', Contact::class)
            ->where('auditable_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
