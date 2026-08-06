<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuggestionRequest extends FormRequest
{
    /**
     * تحديد صلاحية تنفيذ الطلب.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق.
     */
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
                'min:10',
            ],
        ];
    }

    /**
     * رسائل التحقق.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الاقتراح مطلوب.',
            'title.string' => 'عنوان الاقتراح غير صالح.',
            'title.max' => 'عنوان الاقتراح يجب ألا يزيد عن 255 حرفاً.',

            'body.required' => 'تفاصيل الاقتراح مطلوبة.',
            'body.string' => 'تفاصيل الاقتراح غير صالحة.',
            'body.min' => 'تفاصيل الاقتراح يجب ألا تقل عن 10 أحرف.',
        ];
    }
}