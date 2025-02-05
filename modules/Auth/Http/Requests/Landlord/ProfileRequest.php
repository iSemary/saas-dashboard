<?php

namespace Modules\Auth\Http\Requests\Landlord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
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
                ]);

            case 'security':
                return array_merge($rules, [
                    'current_password' => 'required',
                    'new_password' => 'required|min:8|confirmed',
                ]);

            case 'preferences':
                return array_merge($rules, [
                    'theme_mode' => 'required|in:1,2,3',
                ]);

            default:
                return $rules;
        }
    }

    public function messages()
    {
        return [
            'current_password.required' => translate('current_password_is_required'),
            'new_password.required' => translate('new_password_is_required'),
            'new_password.min' => translate('new_password_must_be_at_least_8_characters'),
            'new_password.confirmed' => translate('password_confirmation_does_not_match'),
            'theme_mode.in' => translate('invalid_theme_mode_selected'),
        ];
    }
}
