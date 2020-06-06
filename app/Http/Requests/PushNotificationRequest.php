<?php

namespace App\Http\Requests;

use App\Enums\HttpStatusCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class PushNotificationRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string',
            'body' => 'required|string',
            'send_to_type' => 'required|string',
            'is_interactive' => 'required|string',
            'recipients.*' => 'string',
            'data' => 'required',
            'data.cid' => 'required',
            'data.url' => 'required|string',
            'data.component' => 'required|string',
        ];

        return $rules;
    }

    /**
     * Converting request data to json
     *
     * @return mixed
     */
    public function all($keys = null)
    {
        if (empty($keys)) {
            return parent::json()->all();
        }

        return collect(parent::json()->all())->only($keys)->toArray();
    }

    /**
     * Validation error response
     *
     * @param Validator $validator
     */
    protected function failedValidation($validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $transformed_errors = [];

        foreach ($errors as $field => $message) {
            $split = explode(" ", $message[0]);

            if ($split[count($split) - 1] == 'required.') {
                $errCode = 'ERR_1001';
            } else {
                $errCode = 'ERR_1002';
            }

            $transformed_errors[] = [
                'code'    => $errCode,
                'message' => $message[0]
            ];
        }

        throw new HttpResponseException(response()->json([
            'status' => 'FAIL',
            'errors'  => $transformed_errors,
        ], Response::HTTP_BAD_REQUEST));
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'recipients.*' => 'The phone number in recipients field must be string',
        ];
    }
}
