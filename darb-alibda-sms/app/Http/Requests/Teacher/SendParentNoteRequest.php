<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class SendParentNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'distinct', 'exists:students,id'],
            'section_ids' => ['nullable', 'array'],
            'section_ids.*' => ['integer', 'distinct', 'exists:sections,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls,ppt,pptx,zip,rar', 'max:20480'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $studentIds = $this->input('student_ids', []);
            $sectionIds = $this->input('section_ids', []);

            if (empty($studentIds) && empty($sectionIds)) {
                $validator->errors()->add('student_ids', 'يجب إرسال معرف طالب واحد على الأقل أو معرف شعبة واحدة على الأقل.');
                $validator->errors()->add('section_ids', 'يجب إرسال معرف طالب واحد على الأقل أو معرف شعبة واحدة على الأقل.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.required' => 'العنوان مطلوب.',
            'title.string' => 'العنوان يجب أن يكون نصاً.',
            'title.max' => 'العنوان لا يجب أن يتجاوز 255 حرفاً.',
            'content.required' => 'المحتوى مطلوب.',
            'content.string' => 'المحتوى يجب أن يكون نصاً.',
            'student_ids.array' => 'يجب أن تكون قائمة الطلاب مصفوفة.',
            'student_ids.*.integer' => 'معرّف الطالب يجب أن يكون رقماً صحيحاً.',
            'student_ids.*.distinct' => 'يجب أن يكون كل معرّف طالب فريداً.',
            'student_ids.*.exists' => 'أحد الطلاب المحددين غير موجود.',
            'section_ids.array' => 'يجب أن تكون قائمة الشعب مصفوفة.',
            'section_ids.*.integer' => 'معرّف الشعبة يجب أن يكون رقماً صحيحاً.',
            'section_ids.*.distinct' => 'يجب أن يكون كل معرّف شعبة فريداً.',
            'section_ids.*.exists' => 'أحد الشعب المحددة غير موجودة.',
            'attachments.array' => 'المرفقات يجب أن تكون مصفوفة.',
            'attachments.*.file' => 'كل مرفق يجب أن يكون ملفاً صالحاً.',
            'attachments.*.mimes' => 'نوع الملف غير مدعوم. الأنواع المسموح بها: jpg, jpeg, png, pdf, doc, docx, xlsx, xls, ppt, pptx, zip, rar.',
            'attachments.*.max' => 'الحجم الأقصى لكل مرفق هو 20MB.',
        ];
    }
}
