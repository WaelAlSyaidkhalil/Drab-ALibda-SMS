<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class TeacherMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('markId') !== null;

        if ($isUpdate) {
            // ✔️ عند التعديل: نحتاج فقط mark
            return [
                'mark' => ['required', 'numeric', 'min:0'],
            ];
        }

        // ✔️ عند الإضافة: تبقى القواعد كما هي
        return [
            'enrollment_id' => ['nullable', 'integer', 'exists:student_enrollments,id'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],

            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'subject_component_id' => ['required', 'integer', 'exists:subject_components,id'],
            'term_id' => ['required', 'integer', 'exists:terms,id'],

            'mark' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $isUpdate = $this->route('markId') !== null;

            if (!$isUpdate) {
                // ✔️ عند الإضافة فقط
                $hasEnrollment = filled($this->input('enrollment_id'));
                $hasStudentContext = filled($this->input('student_id')) &&
                                     (filled($this->input('section_id')) || filled($this->input('class_id')));

                if (!$hasEnrollment && !$hasStudentContext) {
                    $validator->errors()->add(
                        'student_id',
                        'يجب إرسال معرف الطالب مع الشعبة أو الصف المطلوب أو إرسال enrollment_id مباشرة.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'mark.required' => 'العلامة مطلوبة.',
            'mark.numeric' => 'العلامة يجب أن تكون رقمية.',
            'mark.min' => 'العلامة لا يمكن أن تقل عن 0.',

            // رسائل الإضافة تبقى كما هي
            'enrollment_id.integer' => 'معرّف التسجيل يجب أن يكون رقماً صحيحاً.',
            'enrollment_id.exists' => 'التسجيل المحدد غير موجود.',
            'student_id.integer' => 'معرّف الطالب يجب أن يكون رقماً صحيحاً.',
            'student_id.exists' => 'الطالب المحدد غير موجود.',
            'class_id.integer' => 'معرّف الصف يجب أن يكون رقماً صحيحاً.',
            'class_id.exists' => 'الصف المحدد غير موجود.',
            'section_id.integer' => 'معرّف الشعبة يجب أن يكون رقماً صحيحاً.',
            'section_id.exists' => 'الشعبة المحددة غير موجودة.',
            'subject_id.required' => 'معرّف المادة مطلوب.',
            'subject_id.integer' => 'معرّف المادة يجب أن يكون رقماً صحيحاً.',
            'subject_id.exists' => 'المادة المحددة غير موجودة.',
            'subject_component_id.required' => 'معرّف مكون المادة مطلوب.',
            'subject_component_id.integer' => 'معرّف مكون المادة يجب أن يكون رقماً صحيحاً.',
            'subject_component_id.exists' => 'مكوّن المادة المحدد غير موجود.',
            'term_id.required' => 'معرّف الفصل الدراسي مطلوب.',
            'term_id.integer' => 'معرّف الفصل الدراسي يجب أن يكون رقماً صحيحاً.',
            'term_id.exists' => 'الفصل الدراسي المحدد غير موجود.',
        ];
    }
}
