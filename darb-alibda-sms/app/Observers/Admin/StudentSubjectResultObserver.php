<?php

namespace App\Observers\Admin;

use App\Enums\StudentStatus;
use App\Models\Grading\StudentSubjectResult;

class StudentSubjectResultObserver
{
    /**
     * Handle the StudentSubjectResult "created" event.
     */
    public function created(StudentSubjectResult $studentSubjectResult): void
    {
        $this->updateEnrollmentFinalResult($studentSubjectResult);
    }

    /**
     * Handle the StudentSubjectResult "updated" event.
     */
    public function updated(StudentSubjectResult $studentSubjectResult): void
    {
        $this->updateEnrollmentFinalResult($studentSubjectResult);
    }

    /**
     * Handle the StudentSubjectResult "deleted" event.
     */
    public function deleted(StudentSubjectResult $studentSubjectResult): void
    {
        $this->updateEnrollmentFinalResult($studentSubjectResult);
    }

    /**
     * Handle the StudentSubjectResult "restored" event.
     */
    public function restored(StudentSubjectResult $studentSubjectResult): void
    {
        //
    }

    /**
     * Handle the StudentSubjectResult "force deleted" event.
     */
    public function forceDeleted(StudentSubjectResult $studentSubjectResult): void
    {
        //
    }

    public function saving(StudentSubjectResult $studentSubjectResult): void
    {
        $studentSubjectResult->yearly_mark = $studentSubjectResult->calculateYearlyMark();

        $studentSubjectResult->result = $studentSubjectResult->calculateResult();
    }

    protected function updateEnrollmentFinalResult(StudentSubjectResult $result): void
    {
        $enrollment = $result->enrollment;

        if($enrollment->status !== StudentStatus::ACTIVE)
            return;

        $enrollment->setFinalAverageAndFinalResult();
    }
}
