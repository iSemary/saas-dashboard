<?php

namespace Modules\HR\Presentation\Http\Requests;

class UpdateJobOpeningRequest extends StoreJobOpeningRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        foreach ($rules as $key => $rule) {
            $rules[$key] = array_values(array_filter((array) $rule, fn ($item) => $item !== 'required'));
            array_unshift($rules[$key], 'sometimes');
        }

        return $rules;
    }
}
