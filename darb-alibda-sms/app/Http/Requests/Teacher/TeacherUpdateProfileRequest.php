<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TeacherUpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'email' => [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone_alt' => ['sometimes', 'nullable', 'string', 'max:20'],
            'experience_years' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            'avatar.image' => 'يجب أن تكون الصورة ملف صورة صالحاً.',
            'avatar.mimes' => 'يجب أن تكون الصورة من نوع jpeg أو jpg أو png أو gif أو webp.',
            'avatar.max' => 'حجم الصورة أكبر من 5 ميغابايت.',
            'fcm_token.string' => 'رمز FCM يجب أن يكون نصاً صالحاً.',
            'fcm_token.max' => 'رمز FCM طويل جداً.',
            'national_id.string' => 'الرقم الوطني يجب أن يكون نصاً صالحاً.',
            'registry_number.string' => 'رقم التسجيل يجب أن يكون نصاً صالحاً.',
            'specialization.string' => 'التخصص يجب أن يكون نصاً صالحاً.',
            'employee_number.string' => 'رقم الموظف يجب أن يكون نصاً صالحاً.',
            'hire_date.date' => 'تاريخ التعيين غير صالح.',
            'employment_type.string' => 'نوع التوظيف غير صالح.',
            'grade.string' => 'الصف غير صالح.',
            'address.string' => 'العنوان غير صالح.',
            'phone_alt.string' => 'رقم الهاتف البديل غير صالح.',
            'experience_years.integer' => 'سنوات الخبرة يجب أن تكون رقماً.',
            'experience_years.min' => 'سنوات الخبرة لا يمكن أن تكون سالبة.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'هناك أخطاء في البيانات المدخلة.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
