<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbsenceJustificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,approved,rejected',
            'review_note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة يجب أن تكون: معلق، مقبول أو مرفوض',
            'review_note.string' => 'ملاحظة المراجعة يجب أن تكون نصاً',
            'review_note.max' => 'ملاحظة المراجعة لا تتجاوز 1000 حرف',
        ];
    }
}
