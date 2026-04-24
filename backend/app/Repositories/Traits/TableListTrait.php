<?php

namespace App\Repositories\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait for handling table listing operations (search, sort, pagination)
 *
 * @template T of Model
 */
trait TableListTrait
{
    /**
     * Apply table listing operations to a query
     *
     * @param Builder<T> $query
     * @param array<string, mixed> $params
     * @param array<string, string> $searchableColumns - Column mapping for search [field => column_name]
     * @param array<string, string> $sortableColumns - Allowed sort columns [field => column_name]
     * @return Builder<T>
     */
    protected function applyTableOperations(
        Builder $query,
        array $params,
        array $searchableColumns = [],
        array $sortableColumns = []
    ): Builder {
        // Apply search
        if (!empty($params['search']) && !empty($searchableColumns)) {
            $search = $params['search'];
            $query->where(function (Builder $q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $field => $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        // Apply filters
        if (!empty($params['filters']) && is_array($params['filters'])) {
            foreach ($params['filters'] as $field => $value) {
                if ($value !== null && $value !== '') {
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
        }

        // Apply sorting
        $sortBy = $params['sort_by'] ?? null;
        $sortDirection = $params['sort_direction'] ?? 'asc';

        if ($sortBy && isset($sortableColumns[$sortBy])) {
            $column = $sortableColumns[$sortBy];
            $query->orderBy($column, $sortDirection);
        } elseif (!empty($sortableColumns)) {
            // Default sort by first sortable column
            $firstColumn = reset($sortableColumns);
            $query->orderBy($firstColumn, 'desc');
        }

        return $query;
    }

    /**
     * Get paginated or all results based on params
     *
     * @param Builder<T> $query
     * @param array<string, mixed> $params
     * @return LengthAwarePaginator<T>|Collection<int, T>
     */
    protected function getResults(Builder $query, array $params): LengthAwarePaginator|Collection
    {
        $perPage = $params['per_page'] ?? 10;

        // Check if should return all (per_page = -1 or 'all')
        if ($perPage === -1 || $perPage === '-1' || $perPage === 'all') {
            return $query->get();
        }

        $page = $params['page'] ?? 1;
        return $query->paginate((int) $perPage, ['*'], 'page', (int) $page);
    }

    /**
     * Standard table list method - combines applyTableOperations and getResults
     *
     * @param class-string<T> $modelClass
     * @param array<string, mixed> $params
     * @param array<string, string> $searchableColumns
     * @param array<string, string> $sortableColumns
     * @return LengthAwarePaginator<T>|Collection<int, T>
     */
    public function tableList(
        string $modelClass,
        array $params,
        array $searchableColumns = [],
        array $sortableColumns = []
    ): LengthAwarePaginator|Collection {
        $query = $modelClass::query();

        $this->applyTableOperations($query, $params, $searchableColumns, $sortableColumns);

        return $this->getResults($query, $params);
    }
}
