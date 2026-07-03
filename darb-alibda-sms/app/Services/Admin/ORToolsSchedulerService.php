<?php

namespace App\Services\Admin;

use App\Enums\DayOfWeek;
use App\Enums\TermType;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Teacher;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Subject;
use App\Models\Subjects\Term;
use App\Models\Traits\HasAcademicYear;
use Symfony\Component\Process\Process;

class ORToolsSchedulerService
{
    use HasAcademicYear;
    public function generateTimetable(): bool
    {
        // 1. Export database to JSON
        $jsonPath = $this->exportData();

        // 2. Execute Python scheduler
        $this->runPythonScheduler($jsonPath);

        // 3. Read generated timetable
        $this->importResult();

        return true;
    }

    public function exportData(): string
    {
        $periods = TimeSlot::ordered()->get();

        $sections = Section::all();

        $teachers = Teacher::all();

        $subjects = Subject::all();

        /*
        |--------------------------------------------------------------------------
        | Build actual timetable slots
        |--------------------------------------------------------------------------
        */

        $slots = [];

        $slotId = 0;

        foreach (DayOfWeek::cases() as $day) {

            foreach ($periods as $period) {

                $slots[] = [

                    'id' => $slotId++,

                    'day' => $day->value,

                    'period_id' => $period->id,

                    'period_number' => $period->period_number->value,

                    'start' => $period->start_time->format('H:i'),

                    'end' => $period->end_time->format('H:i'),
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Build lessons
        |--------------------------------------------------------------------------
        */

        $lessons = [];

        $lessonId = 1;

        foreach ($subjects as $subject) {

            $classSections = $sections
                ->where('class_id', $subject->class_id);

            foreach ($classSections as $section) {
            
                for ($i = 0; $i < $subject->num_of_weekly_hours; $i++) {

                    $lessons[] = [

                        'id' => $lessonId++,

                        'teacher_id' => $subject->teacher_id,

                        'subject_id' => $subject->id,

                        'section_id' => $section->id,

                        'class_id' => $subject->class_id,
                    ];
                }
            }
        }

        $data = [

            'slots' => $slots,

            'lessons' => $lessons,

            'teachers' => $teachers->map(fn ($teacher) => [

                'id' => $teacher->id,

                'name' => $teacher->full_name,

            ])->values(),

            'sections' => $sections->map(fn ($section) => [

                'id' => $section->id,

                'class_id' => $section->class_id,

                'name' => $section->full_name,

            ])->values(),
        ];

        $path = storage_path('app/scheduler');

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file = $path . '/input.json';

        file_put_contents(
            $file,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return $file;
    }

    protected function runPythonScheduler(string $jsonPath): void
    {
        $python = base_path('scheduler/venv/Scripts/python.exe');
        $script = base_path('scheduler/scheduler.py');
        $output = storage_path('app/scheduler/output.json');

        $process = new Process([
            $python,
            $script,
            $jsonPath,
            $output,
        ]);

        $process->setTimeout(300);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    protected function importResult(): void
    {
        $path = storage_path('app/scheduler/output.json');

        if (! file_exists($path)) {
            throw new \RuntimeException('Scheduler did not produce output.json');
        }

        $result = json_decode(file_get_contents($path), true);

        // TODO:
        // 1. Clear existing schedules for the current term
        $firstOrSecond = HasAcademicYear::getCurrentTerm();
        $currentTerm = Term::where('type', TermType::getTermUsingNum($firstOrSecond))
                ->where('academic_year', HasAcademicYear::getCurrentAcademicYear())->first();
        Schedule::where('term_id', $currentTerm->id)->delete();

        // 2. Insert new schedules based on the result
        foreach ($result as $schedule) {
            Schedule::create([
                'term_id' => $currentTerm->id,
                'section_id' => $schedule['section_id'],
                'subject_id' => $schedule['subject_id'],
                'day' => $schedule['day'],
                'time_slot_id' => $schedule['period_number'],
            ]);
        }
    }
}