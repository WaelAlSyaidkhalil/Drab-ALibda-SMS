<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'body' => [
                'required',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'title.required' => 'عنوان الشكوى مطلوب.',

            'title.max' => 'عنوان الشكوى طويل جداً.',

            'body.required' => 'تفاصيل الشكوى مطلوبة.',

            'body.max' => 'تفاصيل الشكوى طويلة جداً.',
        ];
    }
}