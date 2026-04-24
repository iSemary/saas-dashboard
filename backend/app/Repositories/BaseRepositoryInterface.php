<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * Get all records with optional filters
     *
     * @param array $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a record by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Find a record by ID or fail
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail(int $id): Model;

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record by ID
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete a record by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Paginate records with optional filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
