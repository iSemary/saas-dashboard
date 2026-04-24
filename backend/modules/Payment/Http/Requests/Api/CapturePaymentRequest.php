<?php

namespace Modules\Payment\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CapturePaymentRequest extends FormRequest
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
            'amount' => 'nullable|numeric|min:0.01|max:999999.99',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.numeric' => 'Capture amount must be a valid number',
            'amount.min' => 'Capture amount must be at least 0.01',
            'amount.max' => 'Capture amount cannot exceed 999,999.99',
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
                    'message' => 'The given data was invalid',
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
            $amount = $this->input('amount');
            
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
                    return;
                }

                if ($amount && $amount > $transaction->amount) {
                    $validator->errors()->add(
                        'amount',
                        "Capture amount cannot exceed authorized amount ({$transaction->amount})"
                    );
                }
            }
        });
    }
}
