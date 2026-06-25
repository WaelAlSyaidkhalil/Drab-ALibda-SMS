<?php

namespace App\Http\Requests\Parent;

use Illuminate\Foundation\Http\FormRequest;

class ParentExcuseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'absence_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file'],
        ];
    }
}
