<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class PlanStoreRequest extends FormRequest
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
            'name' => 'required|max:32',
            'currency_code' => 'required',
            'price' => 'required|numeric|min:0',
            'is_unlimited_customers' => 'sometimes|boolean',
            'is_unlimited_rewards' => 'sometimes|boolean',
            'is_unlimited_businesses' => 'sometimes|boolean',
            'is_unlimited_staff' => 'sometimes|boolean',
            'is_unlimited_segments' => 'sometimes|boolean',
            'limitations_customers' => 'required_if:is_unlimited_customer,false|nullable|numeric|min:0',
            'limitations_campaigns' => 'required|numeric|min:0',
            'limitations_rewards' => 'required_if:is_unlimited_rewards,false|nullable|numeric|min:0',
            'limitations_businesses' => 'required_if:is_unlimited_businesses,false|nullable|numeric|min:0',
            'limitations_staff' => 'required_if:is_unlimited_staff,false|nullable|numeric|min:0',
            'limitations_segments' => 'required_if:is_unlimited_segments,false|nullable|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Plan name',
            'currency_code' => 'Currency code',
            'price' => 'Price',
            'is_unlimited_customer' => 'Unlimited customers',
            'is_unlimited_rewards' => 'Unlimited rewards',
            'is_unlimited_businesses' => 'Unlimited stores',
            'is_unlimited_staff' => 'Unlimited staff',
            'is_unlimited_segments' => 'Unlimited segments',
            'limitations_customers' => 'Limitations customers',
            'limitations_campaigns' => 'Limitations website',
            'limitations_rewards' => 'Limitations reward offer',
            'limitations_businesses' => 'Limitations stores',
            'limitations_staff' => 'Limitations staffs',
            'limitations_segments' => 'Limitations segments',
        ];
    }
}
