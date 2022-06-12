<?php

namespace App\Http\Requests\SmsTemplate;

use Illuminate\Foundation\Http\FormRequest;

class TestRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return [
                'phone_number' => 'required|numeric',
                'isd_code' => 'required'
            ];
        }
        return [];
    }
}
