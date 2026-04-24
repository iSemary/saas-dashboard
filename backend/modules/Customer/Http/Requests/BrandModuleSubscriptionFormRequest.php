<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandModuleSubscriptionFormRequest extends FormRequest
{
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
            'brand_id' => 'required|integer|exists:brands,id',
            'module_key' => 'required|string|max:255',
            'module_name' => 'required|string|max:255',
            'subscription_status' => 'required|in:active,inactive,suspended,expired',
            'subscription_start' => 'sometimes|date',
            'subscription_end' => 'sometimes|date|after:subscription_start',
            'module_config' => 'sometimes|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_id.required' => translate('brand_id_is_required'),
            'brand_id.exists' => translate('brand_not_found'),
            'module_key.required' => translate('module_key_is_required'),
            'module_name.required' => translate('module_name_is_required') ,
            'subscription_status.required' => translate('subscription_status_is_required'),
            'subscription_status.in' => translate('invalid_subscription_status'),
            'subscription_end.after' => translate('subscription_end_must_be_after_start'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'subscription_start' => $this->subscription_start ?? now(),
        ]);
    }
}
