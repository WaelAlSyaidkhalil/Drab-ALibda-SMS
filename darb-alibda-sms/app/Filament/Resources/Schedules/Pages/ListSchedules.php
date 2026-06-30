<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('dashboard.buttons.create_schedule'))->modalHeading(__('dashboard.buttons.create_schedule')),
        ];
    }

    public function getTitle(): string
    {
        return __('dashboard.pages.schedules');
    }
}
