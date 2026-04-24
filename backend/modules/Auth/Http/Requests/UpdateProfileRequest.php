<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user('api');
        $userId = $user?->id;

        return [
            'name' => 'sometimes|string|max:255',
            'email' => "sometimes|email|max:255|unique:users,email,{$userId}",
            'username' => "sometimes|string|max:255|unique:users,username,{$userId}",
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'timezone' => 'nullable|string|max:255',
        ];
    }
}
