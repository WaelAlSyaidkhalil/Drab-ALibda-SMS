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

        $results = $enrollment?->studentSubjectResults()
            ->with(['subject', 'subject.teacher'])
            ->get();

        $marks = $enrollment?->studentMarks()
            ->with(['subject', 'subjectComponent', 'term'])
            ->orderBy('created_at')
            ->get();

        $subjectGrades = ($results ?? collect())->map(function ($result) use ($semester) {
            return [
                'subject' => $result->subject?->name,
                'assessment_type' => $semester ?? 'yearly',
                'term1_mark' => $result->term1_mark,
                'term2_mark' => $result->term2_mark,
                'mark' => $result->yearly_mark,
                'max_mark' => 100,
                'result' => $result->result?->value ?? $result->result,
                'teacher' => $result->subject?->teacher ? [
                    'id' => $result->subject->teacher->id,
                    'name' => $result->subject->teacher->user?->name,
                ] : null,
                'updated_at' => $result->updated_at?->toDateTimeString(),
            ];
        })->values();

        $componentGrades = ($marks ?? collect())->map(function ($mark) {
            return [
                'subject' => $mark->subject?->name,
                'assessment_type' => $mark->subjectComponent?->type?->value ?? $mark->subjectComponent?->type,
                'mark' => $mark->mark,
                'max_mark' => $mark->subjectComponent?->out_of,
                'term' => $mark->term?->type,
                'date' => $mark->created_at?->toDateString(),
            ];
        })->values();

        return $this->successResponse($subjectGrades, 'تم جلب العلامات بنجاح.', 200, [
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'registry_number' => $student->registry_number,
            ],
            'academic_year' => $enrollment?->academic_year,
            'components' => $componentGrades,
        ]);
    }
}
