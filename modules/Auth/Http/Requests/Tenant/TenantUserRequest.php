<?php

namespace Modules\Auth\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class TenantUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('user');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'username' => [
                'nullable',
                'string',
                'max:64',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'password' => $isUpdate ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'password_confirmation' => $isUpdate ? 'nullable|string|min:8' : 'required|string|min:8',
            'country_id' => 'nullable|exists:countries,id',
            'language_id' => 'nullable|exists:languages,id',
            'factor_authenticate' => 'nullable|boolean',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        // Handle unique validation for email and username
        if ($userId)
        {
            $rules['email'][] = 'unique:users,email,' . $userId;
            if ($this->has('username'))
            {
                $rules['username'][] = 'unique:users,username,' . $userId;
            }
        }
        else
        {
            $rules['email'][] = 'unique:users,email';
            if ($this->has('username'))
            {
                $rules['username'][] = 'unique:users,username';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => @translate('name_required'),
            'name.string' => @translate('name_must_be_string'),
            'name.max' => @translate('name_max_255_characters'),
            'email.required' => @translate('email_required'),
            'email.email' => @translate('email_must_be_valid'),
            'email.unique' => @translate('email_already_exists'),
            'username.regex' => @translate('username_can_only_contain_letters_numbers_underscores'),
            'username.unique' => @translate('username_already_exists'),
            'password.required' => @translate('password_required'),
            'password.min' => @translate('password_must_be_at_least_8_characters'),
            'password.confirmed' => @translate('password_confirmation_does_not_match'),
            'country_id.exists' => @translate('selected_country_does_not_exist'),
            'language_id.exists' => @translate('selected_language_does_not_exist'),
            'roles.array' => @translate('roles_must_be_array'),
            'roles.*.exists' => @translate('selected_role_does_not_exist'),
            'permissions.array' => @translate('permissions_must_be_array'),
            'permissions.*.exists' => @translate('selected_permission_does_not_exist'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Trim whitespace from name, email, username
        $data = [];

        if ($this->has('name'))
        {
            $data['name'] = trim($this->name);
        }

        if ($this->has('email'))
        {
            $data['email'] = trim(strtolower($this->email));
        }

        if ($this->has('username'))
        {
            $data['username'] = trim(strtolower($this->username));
        }

        // Convert factor_authenticate to boolean
        if ($this->has('factor_authenticate'))
        {
            $data['factor_authenticate'] = filter_var($this->factor_authenticate, FILTER_VALIDATE_BOOLEAN);
        }

        if (!empty($data))
        {
            $this->merge($data);
        }
    }
}
