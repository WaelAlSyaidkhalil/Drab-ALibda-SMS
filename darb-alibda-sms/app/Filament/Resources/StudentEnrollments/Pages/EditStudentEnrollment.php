<?php

namespace App\Filament\Resources\StudentEnrollments\Pages;

use App\Filament\Resources\StudentEnrollments\StudentEnrollmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentEnrollment extends EditRecord
{
    protected static string $resource = StudentEnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
