<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'nullable|date',
            'schedule_id' => 'nullable|integer|exists:schedules,id',
            'students' => 'required|array|min:1',
            'students.*.student_id' => 'required|integer|distinct|exists:students,id',
            'students.*.status' => 'required|in:present,absent,late',
        ];
    }

    public function messages(): array
    {
        return [
            'students.required' => 'يجب إرسال قائمة الطلاب المعدلة',
            'students.array' => 'قائمة الطلاب يجب أن تكون مصفوفة',
            'students.min' => 'يجب إرسال طالب واحد على الأقل',
            'students.*.student_id.required' => 'معرّف الطالب مطلوب لكل حالة',
            'students.*.student_id.integer' => 'معرّف الطالب يجب أن يكون رقماً صحيحاً',
            'students.*.student_id.exists' => 'أحد الطلاب غير موجود في النظام',
            'students.*.status.required' => 'حالة الطالب مطلوبة',
            'students.*.status.in' => 'حالة الطالب يجب أن تكون present أو absent أو late',
            'date.date' => 'التاريخ غير صالح',
            'schedule_id.exists' => 'معرّف الحصة غير صالح',
        ];
    }
}
