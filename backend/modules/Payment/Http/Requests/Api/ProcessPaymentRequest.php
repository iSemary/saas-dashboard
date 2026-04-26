<?php

namespace Modules\Payment\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcessPaymentRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'currency' => 'required|string|size:3|exists:currencies,code',
            'customer_id' => 'nullable|string|max:255',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_method_data' => 'nullable|array',
            'payment_method_data.type' => 'required_with:payment_method_data|string|in:card,bank_account,wallet',
            'payment_method_data.card' => 'required_if:payment_method_data.type,card|array',
            'payment_method_data.card.number' => 'required_with:payment_method_data.card|string|min:13|max:19',
            'payment_method_data.card.exp_month' => 'required_with:payment_method_data.card|integer|between:1,12',
            'payment_method_data.card.exp_year' => 'required_with:payment_method_data.card|integer|min:' . date('Y'),
            'payment_method_data.card.cvc' => 'required_with:payment_method_data.card|string|min:3|max:4',
            'payment_method_data.card.name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'billing_address' => 'nullable|array',
            'billing_address.line1' => 'required_with:billing_address|string|max:255',
            'billing_address.line2' => 'nullable|string|max:255',
            'billing_address.city' => 'required_with:billing_address|string|max:100',
            'billing_address.state' => 'nullable|string|max:100',
            'billing_address.postal_code' => 'required_with:billing_address|string|max:20',
            'billing_address.country' => 'required_with:billing_address|string|size:2',
            'shipping_address' => 'nullable|array',
            'shipping_address.line1' => 'required_with:shipping_address|string|max:255',
            'shipping_address.line2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required_with:shipping_address|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'required_with:shipping_address|string|max:20',
            'shipping_address.country' => 'required_with:shipping_address|string|size:2',
            'capture' => 'nullable|boolean',
            'statement_descriptor' => 'nullable|string|max:22',
            'receipt_email' => 'nullable|email|max:255',
            'order_id' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'return_url' => 'nullable|url|max:2048',
            'cancel_url' => 'nullable|url|max:2048',
            'country' => 'nullable|string|size:2',
            'customer_segment' => 'nullable|string|in:all,new,existing,vip,enterprise',
            'is_recurring' => 'nullable|boolean',
            'risk_score' => 'nullable|integer|between:0,100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Payment amount is required',
            'amount.numeric' => 'Payment amount must be a valid number',
            'amount.min' => 'Payment amount must be at least 0.01',
            'amount.max' => 'Payment amount cannot exceed 999,999.99',
            'currency.required' => 'Currency is required',
            'currency.size' => 'Currency must be a 3-letter ISO code',
            'currency.exists' => 'Invalid currency code',
            'payment_method_data.card.number.required_with' => 'Card number is required',
            'payment_method_data.card.number.min' => 'Card number must be at least 13 digits',
            'payment_method_data.card.number.max' => 'Card number cannot exceed 19 digits',
            'payment_method_data.card.exp_month.required_with' => 'Card expiry month is required',
            'payment_method_data.card.exp_month.between' => 'Card expiry month must be between 1 and 12',
            'payment_method_data.card.exp_year.required_with' => 'Card expiry year is required',
            'payment_method_data.card.exp_year.min' => 'Card expiry year cannot be in the past',
            'payment_method_data.card.cvc.required_with' => 'Card CVC is required',
            'payment_method_data.card.cvc.min' => 'Card CVC must be at least 3 digits',
            'payment_method_data.card.cvc.max' => 'Card CVC cannot exceed 4 digits',
            'billing_address.line1.required_with' => 'Billing address line 1 is required',
            'billing_address.city.required_with' => 'Billing city is required',
            'billing_address.postal_code.required_with' => 'Billing postal code is required',
            'billing_address.country.required_with' => 'Billing country is required',
            'billing_address.country.size' => 'Billing country must be a 2-letter ISO code',
            'statement_descriptor.max' => 'Statement descriptor cannot exceed 22 characters',
            'receipt_email.email' => 'Receipt email must be a valid email address',
            'return_url.url' => 'Return URL must be a valid URL',
            'cancel_url.url' => 'Cancel URL must be a valid URL',
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
            // Custom validation: either payment_method_id or payment_method_data must be provided
            if (!$this->payment_method_id && !$this->payment_method_data) {
                $validator->errors()->add(
                    'payment_method',
                    'Either payment_method_id or payment_method_data must be provided'
                );
            }

            // Custom validation: card number Luhn algorithm check
            if ($this->has('payment_method_data.card.number')) {
                $cardNumber = preg_replace('/\D/', '', $this->input('payment_method_data.card.number'));
                if (!$this->isValidCardNumber($cardNumber)) {
                    $validator->errors()->add(
                        'payment_method_data.card.number',
                        'Invalid card number'
                    );
                }
            }

            // Custom validation: expiry date check
            if ($this->has('payment_method_data.card.exp_month') && $this->has('payment_method_data.card.exp_year')) {
                $month = $this->input('payment_method_data.card.exp_month');
                $year = $this->input('payment_method_data.card.exp_year');
                
                $expiryDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
                if ($expiryDate->isPast()) {
                    $validator->errors()->add(
                        'payment_method_data.card.exp_year',
                        'Card has expired'
                    );
                }
            }
        });
    }

    /**
     * Validate card number using Luhn algorithm.
     */
    protected function isValidCardNumber(string $cardNumber): bool
    {
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }

        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $digit = intval($cardNumber[$i]);
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = ($digit % 10) + 1;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }

        return ($sum % 10) === 0;
    }
}
