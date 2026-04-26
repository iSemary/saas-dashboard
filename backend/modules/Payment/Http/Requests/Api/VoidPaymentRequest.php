<?php

namespace Modules\Payment\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VoidPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'metadata' => 'nullable|array',
            'reason' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'reason.max' => 'Void reason cannot exceed 500 characters',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => translate('message.validation_failed'),
                    'details' => $validator->errors(),
                ],
            ], 422)
        );
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $transactionId = $this->route('transactionId');
            
            if ($transactionId) {
                $transaction = \Modules\Payment\Entities\PaymentTransaction::where('transaction_id', $transactionId)
                    ->orWhere('gateway_transaction_id', $transactionId)
                    ->first();

                if (!$transaction) {
                    $validator->errors()->add(
                        'transaction_id',
                        'Transaction not found'
                    );
                    return;
                }

                if ($transaction->status !== 'authorized') {
                    $validator->errors()->add(
                        'transaction_id',
                        'Transaction is not in authorized state'
                    );
                }
            }
        });
    }
}
