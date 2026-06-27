<?php

namespace App\Jobs;

use App\Enums\ClassType;
use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\StudentEnrollment;
use App\Models\Traits\HasAcademicYear;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateStudentEnrollmentsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $current = HasAcademicYear::getCurrentAcademicYear();

        [$start, $end] = explode('-', $current);

        $nextAcademicYear = ($start + 1) . '-' . ($end + 1);
        
        StudentEnrollment::query()
            ->with(['student', 'section.schoolClass'])
            ->active()
            ->chunkById(500, function ($enrollments) use ($nextAcademicYear) {

                foreach ($enrollments as $enrollment) {
                    if($enrollment->final_result === MarkResult::PASS)
                        {
                            if($enrollment->section->schoolClass->type->getGradeLevel() == config('school.last_grade'))
                            {
                                $enrollment->status = StudentStatus::GRADUATED;
                                $enrollment->save();
                                continue;
                            }

                            $enrollment->status = StudentStatus::PROMOTED;
                            $enrollment->save();

                            $currentClass = $enrollment->section->schoolClass;
                            $newClass = SchoolClass::gradeLevel($currentClass->type->getGradeLevel() + 1)->first();
                            $newSection = Section::byName($enrollment->section->name)->where('class_id', $newClass->id)->first();

                            StudentEnrollment::firstOrCreate(
                                [
                                    'student_id' => $enrollment->student->id,
                                    'academic_year' => $nextAcademicYear,
                                ],
                                [
                                    'section_id' => $newSection->id,
                                    'enrollment_date' => now(),
                                    'status' => StudentStatus::ACTIVE,
                                ]
                            );
                        }
                    else if($enrollment->final_result === MarkResult::FAIL)
                    {
                        $enrollment->status = StudentStatus::REPEATED;
                        $enrollment->save();

                        StudentEnrollment::firstOrCreate(
                            [
                                'student_id' => $enrollment->student->id,
                                'academic_year' => $nextAcademicYear,
                            ],
                            [
                                'section_id' => $enrollment->section_id,
                                'enrollment_date' => now(),
                                'status' => StudentStatus::ACTIVE,
                            ]
                        );
                    }
                }
            });
    }
}
