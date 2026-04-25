<?php

namespace Modules\HR\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HR\Domain\ValueObjects\PositionLevel;

class StorePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:positions,code',
            'department_id' => 'nullable|integer|exists:departments,id',
            'level' => 'nullable|string|in:' . implode(',', array_column(PositionLevel::cases(), 'value')),
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'description' => 'nullable|string|max:2000',
            'requirements' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            'custom_fields' => 'nullable|array',
        ];
    }
}
