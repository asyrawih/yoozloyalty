<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class BillingConfirmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required',
            'payment_method' => 'required',
            'amount' => 'required|numeric',
            'merchant_bank_name' => 'required_if:payment_method,cheque,bank_transfer',
            'merchant_identifier' => 'required',
            'amount_paid' => 'required|numeric|same:amount',
            'paid_at' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf'
        ];
    }
}
