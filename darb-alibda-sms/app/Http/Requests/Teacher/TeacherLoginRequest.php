<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'bail',
                'required',
                'string',
                'regex:/^\+?\d{10,15}$/',
                Rule::exists('users', 'phone'),
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:64',
                'regex:/^\S+$/',
            ],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->phone ? preg_replace('/[^0-9+]/', '', $this->phone) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'الرجاء كتابة رقم الجوال قبل المتابعة.',
            'phone.string' => 'رقم الجوال يجب أن يكون نصاً صالحاً.',
            'phone.regex' => 'استخدم صيغة رقم جوال صحيحة بدون مسافات أو أحرف غير رقمية.',
            'phone.exists' => 'هذا الرقم غير مسجل لدينا. تحقق من رقم الجوال أو تواصل مع الدعم.',
            'password.required' => 'الرجاء كتابة كلمة المرور للمتابعة.',
            'password.string' => 'كلمة المرور يجب أن تكون نصاً صالحاً.',
            'password.min' => 'كلمة المرور لا يمكن أن تكون أقل من 8 أحرف.',
            'password.max' => 'كلمة المرور طويلة جداً. الحد الأقصى 64 حرفاً.',
            'password.regex' => 'كلمة المرور لا يمكن أن تحتوي على مسافات فارغة.',
            'remember.boolean' => 'الخيار تذكرني يجب أن يكون صحيحاً أو خطأً.',
        ];
    }
}
