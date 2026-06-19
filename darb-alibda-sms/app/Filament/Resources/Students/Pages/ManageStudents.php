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
            CreateAction::make()
        ];
    }

    public function getTitle(): string
    {
        return 'الطلاب';
    }
}
