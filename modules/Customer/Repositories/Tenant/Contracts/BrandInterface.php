<?php

namespace Modules\Customer\Repositories\Tenant\Contracts;

interface BrandInterface
{
    /**
     * Get all brands
     */
    public function getAll(array $conditions = []);

    /**
     * Get brands for DataTables
     */
    public function getDataTables();

    /**
     * Get a brand by ID
     */
    public function get(int $id);

    /**
     * Create a new brand
     */
    public function create(array $data);

    /**
     * Update a brand
     */
    public function update(int $id, array $data);

    /**
     * Delete a brand
     */
    public function delete(int $id);

    /**
     * Restore a brand
     */
    public function restore(int $id);

    /**
     * Get brands with their modules
     */
    public function getBrandsWithModules();

    /**
     * Get modules for a specific brand
     */
    public function getBrandModules(int $brandId);

    /**
     * Assign modules to a brand
     */
    public function assignModules(int $brandId, array $moduleIds);

    /**
     * Search brands
     */
    public function search(string $search);

    /**
     * Get brands for dashboard display
     */
    public function getBrandsForDashboard();
}
