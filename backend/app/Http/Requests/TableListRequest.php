<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableListRequest extends FormRequest
{
    /**
     * Default values for table listing
     */
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 10;
    public const MAX_PER_PAGE = 100;
    public const DEFAULT_SORT_DIRECTION = 'asc';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:' . self::MAX_PER_PAGE],
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', 'max:100'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
            'filters' => ['nullable', 'array'],
        ];
    }

    /**
     * Get validated table parameters with defaults
     */
    public function getTableParams(): array
    {
        $validated = $this->validated();

        return [
            'page' => $validated['page'] ?? self::DEFAULT_PAGE,
            'per_page' => $validated['per_page'] ?? self::DEFAULT_PER_PAGE,
            'search' => $validated['search'] ?? null,
            'sort_by' => $validated['sort_by'] ?? null,
            'sort_direction' => $validated['sort_direction'] ?? self::DEFAULT_SORT_DIRECTION,
            'filters' => $validated['filters'] ?? [],
        ];
    }

    /**
     * Check if should return all records (per_page = -1 or 0)
     */
    public function shouldReturnAll(): bool
    {
        $perPage = $this->input('per_page');
        return $perPage === '-1' || $perPage === 'all' || $perPage === 0 || $perPage === '0';
    }
}
