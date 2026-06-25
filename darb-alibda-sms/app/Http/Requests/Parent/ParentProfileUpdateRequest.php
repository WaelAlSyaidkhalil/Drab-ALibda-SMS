<?php

namespace App\Http\Requests\Parent;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ParentProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'regex:/^\+?\d{10,15}$/', Rule::unique('users', 'phone')->ignore($userId)],
            'profile_image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone_number')) {
            $this->merge([
                'phone_number' => $this->phone_number ? preg_replace('/[^0-9+]/', '', $this->phone_number) : null,
            ]);
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
