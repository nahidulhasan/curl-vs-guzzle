<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateNumberRequest extends FormRequest
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
            "numbers"   => 'required|array',
            "numbers.*" => ['required', 'regex:/(01)[0-9]{9}/', 'min:11', 'max:11'],
        ];
    }

    public function messages()
    {
        return [
            'numbers.*.regex' => 'Enter Valid Mobile Number(s)',
            'numbers.*.min' => 'Enter Valid Mobile Number(s)',
            'numbers.*.max' => 'Enter Valid Mobile Number(s)',
        ];
    }
}
