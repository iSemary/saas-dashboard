<?php

namespace Modules\Payment\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcessRefundRequest extends FormRequest
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
            'transaction_id' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'reason' => 'nullable|string|in:requested_by_customer,duplicate,fraudulent,subscription_cancellation,other',
            'reason_details' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'refund_fees' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'transaction_id.required' => 'Transaction ID is required',
            'transaction_id.string' => 'Transaction ID must be a string',
            'transaction_id.max' => 'Transaction ID cannot exceed 255 characters',
            'amount.required' => 'Refund amount is required',
            'amount.numeric' => 'Refund amount must be a valid number',
            'amount.min' => 'Refund amount must be at least 0.01',
            'amount.max' => 'Refund amount cannot exceed 999,999.99',
            'reason.in' => 'Invalid refund reason',
            'reason_details.max' => 'Reason details cannot exceed 500 characters',
            'refund_fees.boolean' => 'Refund fees must be true or false',
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
            // Check if transaction exists and can be refunded
            $transactionId = $this->input('transaction_id');
            $amount = $this->input('amount');
            
            if ($transactionId && $amount) {
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

                if (!$transaction->canBeRefunded()) {
                    $validator->errors()->add(
                        'transaction_id',
                        'Transaction cannot be refunded'
                    );
                    return;
                }

                $refundableAmount = $transaction->getRefundableAmount();
                if ($amount > $refundableAmount) {
                    $validator->errors()->add(
                        'amount',
                        "Refund amount cannot exceed refundable amount ({$refundableAmount})"
                    );
                }
            }
        });
    }
}
