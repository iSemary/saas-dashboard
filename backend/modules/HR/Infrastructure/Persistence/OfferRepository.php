<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\Offer;

class OfferRepository implements OfferRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Offer::query()->with(['application', 'candidate', 'jobOpening', 'sender']);

        if (!empty($filters['application_id'])) {
            $query->where('application_id', $filters['application_id']);
        }

        if (!empty($filters['candidate_id'])) {
            $query->where('candidate_id', $filters['candidate_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Offer
    {
        return Offer::with(['application', 'candidate', 'jobOpening', 'sender'])->findOrFail($id);
    }

    public function create(array $data): Offer
    {
        return Offer::create($data);
    }

    public function update(int $id, array $data): Offer
    {
        $offer = $this->findOrFail($id);
        $offer->update($data);
        return $offer->fresh();
    }

    public function delete(int $id): bool
    {
        return Offer::destroy($id) > 0;
    }
}
