<?php

namespace Modules\Auth\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $user = auth()->user();
        $type = $this->input('type');

        $rules = [
            'type' => 'required|in:general,security,preferences'
        ];

        switch ($type) {
            case 'general':
                return array_merge($rules, [
                    'name' => 'required_without:remove_avatar|string|max:255',
                    'username' => ['required_without:remove_avatar', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                    'email' => ['required_without:remove_avatar', 'email', Rule::unique('users')->ignore($user->id)],
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:255',
                    'gender' => 'nullable|in:male,female,other',
                    'country_id' => 'nullable|exists:countries,id',
                    'language_id' => 'nullable|exists:languages,id',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1048',
                    'remove_avatar' => 'nullable|boolean',
                    'birthdate' => 'nullable|date|before:today',
                    'home_street_1' => 'nullable|min:1|max:1024',
                    'home_street_2' => 'nullable|min:1|max:1024',
                    'home_building_number' => 'nullable|min:1|max:255',
                    'home_landmark' => 'nullable|min:1|max:255',
                    'timezone' => 'nullable|min:1|max:255',
                ]);

            case 'security':
                return array_merge($rules, [
                    'current_password' => 'required',
                    'new_password' => 'required|min:8|confirmed',
                ]);

            case 'preferences':
                return array_merge($rules, [
                    'notifications_email' => 'nullable|boolean',
                    'notifications_push' => 'nullable|boolean',
                    'notifications_sms' => 'nullable|boolean',
                    'theme_mode' => 'nullable|in:light,dark',
                    'language_id' => 'nullable|exists:languages,id',
                    'timezone' => 'nullable|string|max:255',
                    'date_format' => 'nullable|string|max:20',
                    'time_format' => 'nullable|in:12,24',
                ]);

            default:
                return $rules;
        }
    }

    public function messages(): array
    {
        return [
            'type.required' => @translate('type_is_required'),
            'type.in' => @translate('invalid_type_selected'),
            'name.required_without' => @translate('name_is_required'),
            'username.required_without' => @translate('username_is_required'),
            'username.unique' => @translate('username_already_taken'),
            'email.required_without' => @translate('email_is_required'),
            'email.email' => @translate('valid_email_required'),
            'email.unique' => @translate('email_already_taken'),
            'current_password.required' => @translate('current_password_required'),
            'new_password.required' => @translate('new_password_required'),
            'new_password.min' => @translate('password_min_length'),
            'new_password.confirmed' => @translate('password_confirmation_mismatch'),
        ];
    }
}
