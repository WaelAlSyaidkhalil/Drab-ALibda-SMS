<?php

namespace App\Filament\Resources\StudentEnrollments\Pages;

use App\Filament\Resources\StudentEnrollments\StudentEnrollmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentEnrollment extends CreateRecord
{
    protected static string $resource = StudentEnrollmentResource::class;

    public function getTitle(): string
    {
        return __('dashboard.buttons.create_student_enrollment');
    }
}
