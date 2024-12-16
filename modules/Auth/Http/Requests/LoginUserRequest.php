<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'remember_me' => filter_var($this->remember_me, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subdomain' => 'required|max:64|exists:tenants,name|min:2|regex:/^[a-zA-Z0-9]+$/',
            'username' => 'required|max:255',
            'password' => 'required|max:255|min:8',
            'remember_me' => 'required|boolean',
        ];
    }
}
