<?php

namespace App\Http\Requests\NoticationServiceSettings;

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

        if ($this->scheme) {
            foreach ($this->scheme as $key => $value) {
                if (!$value) {
                    $rules[$key] = 'required';
                }
            }
        }

        $rules['service_name'] = 'required';
        $rules['status'] = 'required';

        return $rules;
    }
}
