<?php

namespace App\Filament\Resources\TimeSlots\Pages;

use App\Filament\Resources\TimeSlots\TimeSlotResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;

class ManageTimeSlots extends ManageRecords
{
    protected static string $resource = TimeSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
        ];
    }
}
