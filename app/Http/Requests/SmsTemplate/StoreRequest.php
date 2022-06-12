<?php

namespace App\Http\Requests\SmsTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        $rules = [];

        // $rules['id'] = 'required';
        $rules['template'] = 'required';
        $rules['type'] = 'required';
        $rules['current_password'] = 'required';

        return $rules;
    }
}
