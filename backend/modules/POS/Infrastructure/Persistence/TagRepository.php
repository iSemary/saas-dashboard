<?php

namespace Modules\POS\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Collection;
use Modules\POS\Domain\Contracts\TagRepositoryInterface;
use Modules\POS\Domain\Entities\Tag;

class TagRepository implements TagRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        $query = Tag::with('creator');
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        return $query->orderBy('value')->get();
    }

    public function findOrFail(int $id): Tag
    {
        return Tag::findOrFail($id);
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Tag::findOrFail($id)->delete();
    }
}
