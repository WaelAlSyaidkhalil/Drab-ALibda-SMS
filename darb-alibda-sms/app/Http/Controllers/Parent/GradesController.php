<?php

namespace App\Http\Controllers\Parent;

use App\Models\Academic\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradesController extends ParentController
{
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $academicYear = $request->query('academic_year');
        $semester = $request->query('semester');

        $enrollment = $student->enrollments()
            ->when($academicYear, fn ($q) => $q->where('academic_year', $academicYear))
            ->latest('created_at')
            ->first();

        $results = $enrollment?->studentSubjectResults()->with('subject')->get();

        return $this->successResponse(($results ?? collect())->map(fn ($result) => [
            'subject' => $result->subject->name,
            'exam_type' => $semester ?? 'yearly',
            'grade' => $result->yearly_mark,
            'max_grade' => 100,
            'exam_date' => $result->updated_at?->toDateString(),
        ])->values(), 'تم جلب العلامات بنجاح.');
    }
}
