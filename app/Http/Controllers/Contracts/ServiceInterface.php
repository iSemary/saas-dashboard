<?php

namespace App\Http\Controllers\Contracts;

/**
 * Service Interface following Dependency Inversion Principle
 * Defines the contract for all services
 */
interface ServiceInterface
{
    /**
     * Get all resources
     */
    public function getAll(array $conditions = []);

    /**
     * Get data tables
     */
    public function getDataTables();

    /**
     * Get a resource by ID
     */
    public function get(int $id);

    /**
     * Create a new resource
     */
    public function create(array $data);

    /**
     * Update a resource
     */
    public function update(int $id, array $data);

    /**
     * Delete a resource
     */
    public function delete(int $id);

    /**
     * Restore a resource
     */
    public function restore(int $id);
}
