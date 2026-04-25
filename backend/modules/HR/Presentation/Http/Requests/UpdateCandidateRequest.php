<?php

namespace Modules\HR\Presentation\Http\Requests;

class UpdateCandidateRequest extends StoreCandidateRequest
{
    public function rules(): array
    {
        $candidateId = (int) $this->route('id');

        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', "unique:candidates,email,{$candidateId}"],
            'phone' => ['sometimes', 'nullable', 'string', 'max:100'],
            'current_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'current_company' => ['sometimes', 'nullable', 'string', 'max:255'],
            'source' => ['sometimes', 'nullable', 'string', 'max:100'],
            'resume_path' => ['sometimes', 'nullable', 'string', 'max:500'],
            'blacklisted' => ['sometimes', 'boolean'],
            'blacklist_reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
