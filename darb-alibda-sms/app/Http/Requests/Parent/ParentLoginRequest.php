<?php

namespace App\Http\Requests\Parent;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ParentLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'regex:/^\+?\d{10,15}$/',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:64',
            ],

            'fcm_token' => [
                'sometimes',
                'nullable',
                'string',
                'max:2048',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $phone = $this->phone ?? $this->phone_number;

        $this->merge([
            'phone' => $phone
                ? preg_replace('/[^0-9+]/', '', $phone)
                : null,
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}