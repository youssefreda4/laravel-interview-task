<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'numeric'],
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('The mobile number is required.'),
            'mobile.numeric' => __('The mobile number must be numeric.'),
            'password.required' => __('The password is required.'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new ValidationException($validator, response()->json([
                'status' => 'error',
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE));
        }


        throw new ValidationException($validator);
    }
}
