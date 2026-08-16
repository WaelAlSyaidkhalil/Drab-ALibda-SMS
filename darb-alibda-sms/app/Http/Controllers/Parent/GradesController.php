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

        $enrollment = $student->enrollments()
            ->when(
                $academicYear,
                fn ($q) => $q->where('academic_year', $academicYear)
            )
            ->latest('created_at')
            ->first();

        if (! $enrollment) {
            return $this->successResponse(
                [],
                'لا توجد علامات للطالب.',
                200,
                [
                    'student' => [
                        'id' => $student->id,
                        'full_name' => $student->full_name,
                        'registry_number' => $student->registry_number,
                    ],
                    'academic_year' => null,
                    'enrollment_id' => null,
                    'subjects_count' => 0,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | جلب النتائج النهائية للمواد
        |--------------------------------------------------------------------------
        */
        $results = $enrollment->studentSubjectResults()
            ->with([
                'subject',
                'subject.teacher.user',
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | جلب علامات المكونات
        |--------------------------------------------------------------------------
        */
        $marks = $enrollment->studentMarks()
            ->with([
                'subject',
                'subjectComponent',
                'term',
            ])
            ->orderBy('subject_id')
            ->orderBy('term_id')
            ->orderBy('subject_component_id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | تجميع العلامات حسب المادة
        |--------------------------------------------------------------------------
        */
        $grades = $results->map(function ($result) use ($marks) {

            $subjectMarks = $marks
                ->where('subject_id', $result->subject_id);

            /*
            |--------------------------------------------------------------------------
            | تجميع العلامات حسب الفصل
            |--------------------------------------------------------------------------
            */
            $terms = $subjectMarks
                ->groupBy('term_id')
                ->map(function ($termMarks, $termId) {

                    $term = $termMarks->first()?->term;

                    $components = $termMarks->map(function ($mark) {

                        $component = $mark->subjectComponent;

                        $maxMark = $component?->out_of ?? 0;
                        $markValue = $mark->mark;

                        return [
                            'id' => $component?->id,

                            'name' =>
                                $component?->description
                                ?? $component?->type?->value
                                ?? $component?->type,

                            'type' =>
                                $component?->type?->value
                                ?? $component?->type,

                            'mark' => $markValue,

                            'max_mark' => $maxMark,

                            'percentage' => $maxMark > 0
                                ? round(($markValue / $maxMark) * 100, 2)
                                : 0,

                            'date' => $mark->created_at?->toDateTimeString(),
                        ];
                    })->values();

                    /*
                    |--------------------------------------------------------------------------
                    | مجموع مكونات الفصل
                    |--------------------------------------------------------------------------
                    */
                    $totalMark = $components->sum('mark');
                    $maxMark = $components->sum('max_mark');

                    return [
                        'term_id' => (int) $termId,

                        'term' =>
                            $term?->type?->value
                            ?? $term?->type,

                        'components' => $components,

                        'total_mark' => round($totalMark, 2),

                        'max_mark' => round($maxMark, 2),

                        'percentage' => $maxMark > 0
                            ? round(($totalMark / $maxMark) * 100, 2)
                            : 0,
                    ];
                })
                ->values();

            /*
            |--------------------------------------------------------------------------
            | المعلم
            |--------------------------------------------------------------------------
            */
            $teacher = $result->subject?->teacher;

            return [
                'subject' => [
                    'id' => $result->subject?->id,
                    'name' => $result->subject?->name,
                    'code' => $result->subject?->code,
                ],

                'teacher' => $teacher ? [
                    'id' => $teacher->id,
                    'name' => $teacher->user?->name,
                ] : null,

                'terms' => $terms,

                /*
                |--------------------------------------------------------------------------
                | النتيجة النهائية
                |--------------------------------------------------------------------------
                */
                'final_result' => [
                    'term1_mark' => $result->term1_mark,
                    'term2_mark' => $result->term2_mark,
                    'yearly_mark' => $result->yearly_mark,

                    'result' =>
                        $result->result?->value
                        ?? $result->result,

                    'pass_mark' => $result->subject?->pass_mark,

                    'performance_level' =>
                        $result->performance_level,
                ],

                'updated_at' =>
                    $result->updated_at?->toDateTimeString(),
            ];
        })->values();

        return $this->successResponse(
            $grades,
            'تم جلب العلامات بنجاح.',
            200,
            [
                'student' => [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'registry_number' => $student->registry_number,
                ],

                'academic_year' => $enrollment->academic_year,

                'enrollment_id' => $enrollment->id,

                'subjects_count' => $grades->count(),
            ]
        );
    }
}