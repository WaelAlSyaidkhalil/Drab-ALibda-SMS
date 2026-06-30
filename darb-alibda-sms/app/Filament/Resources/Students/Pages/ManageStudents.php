<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;

class ManageStudents extends ManageRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('dashboard.buttons.create_student'))->modalHeading(__('dashboard.buttons.create_student'))
        ];
    }

    public function getTitle(): string
    {
        return __('dashboard.pages.students_info');
    }
}
