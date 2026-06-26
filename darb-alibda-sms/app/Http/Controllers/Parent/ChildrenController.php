<?php

namespace App\Http\Controllers\Parent;

use App\Http\Resources\Parent\ChildResource;
use App\Models\Academic\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildrenController extends ParentController
{
    public function index(Request $request): JsonResponse
    {
        $children = Student::query()
            ->byParent($request->user()->id)
            ->with(['enrollments' => fn ($query) => $query->active()->with('section.schoolClass')])
            ->get();

        return $this->successResponse($children->map(fn (Student $student) => new ChildResource($student))->values(), 'تم جلب الأبناء بنجاح.');
    }

    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        return $this->successResponse([
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'personal_information' => [
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'father_name' => $student->father_name,
                    'mother_name' => $student->mother_name,
                    'birth_date' => $student->birth_date?->toDateString(),
                    'gender' => $student->gender,
                ],
                'academic_information' => [
                    'classroom' => $student->getCurrentClass()?->name,
                    'section' => $student->getCurrentSection()?->name,
                    'academic_year' => $student->getCurrentEnrollment()?->academic_year,
                ],
                'attendance_summary' => [
                    'present_days' => 0,
                    'absent_days' => 0,
                    'excused_absences' => 0,
                    'unexcused_absences' => 0,
                    'attendance_percentage' => 0,
                ],
            ],
        ], 'تم جلب تفاصيل الطالب بنجاح.');
    }
}
