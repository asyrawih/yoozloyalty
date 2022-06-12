<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class BillingCheckoutRequest extends FormRequest
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
            'plan_id' => 'required',
            'payment_method' => 'required|string',
            'bank_id' => 'required_if:payment_method,cheque,bank_transfer',
            'amount' => 'required|numeric',
            'currency' => 'required',
            'action' => 'required|string|in:buy,change,upgrade'
        ];
    }
}
