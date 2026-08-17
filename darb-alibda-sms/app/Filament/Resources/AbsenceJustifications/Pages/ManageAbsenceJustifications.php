<?php

namespace App\Filament\Resources\AbsenceJustifications\Pages;

use App\Filament\Resources\AbsenceJustifications\AbsenceJustificationResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAbsenceJustifications extends ManageRecords
{
    protected static string $resource = AbsenceJustificationResource::class;

    public function getTitle(): string
    {
        return __('dashboard.pages.absence_justification');
    }
}
