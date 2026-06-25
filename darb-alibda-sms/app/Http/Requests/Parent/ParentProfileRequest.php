<?php

namespace App\Http\Requests\Parent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParentProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^\+?\d{10,15}$/', Rule::unique('users', 'phone')->ignore($this->user()?->id)],
            'profile_image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }
}
