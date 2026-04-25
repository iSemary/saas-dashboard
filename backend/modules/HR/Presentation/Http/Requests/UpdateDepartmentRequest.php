<?php

namespace Modules\HR\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HR\Domain\ValueObjects\DepartmentStatus;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('id');
        return [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $departmentId,
            'parent_id' => 'nullable|integer|exists:departments,id|not_in:' . $departmentId,
            'manager_id' => 'nullable|integer|exists:employees,id',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|required|string|in:' . implode(',', array_column(DepartmentStatus::cases(), 'value')),
            'custom_fields' => 'nullable|array',
        ];
    }
}
