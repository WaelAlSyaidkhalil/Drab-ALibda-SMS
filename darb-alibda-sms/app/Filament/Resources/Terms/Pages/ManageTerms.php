<?php

namespace App\Filament\Resources\Terms\Pages;

use App\Filament\Resources\Terms\TermResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTerms extends ManageRecords
{
    protected static string $resource = TermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalHeading(__('dashboard.buttons.create_term'))->label(__('dashboard.buttons.create_term')),
        ];
    }

    public function getTitle(): string
    {
        return __('dashboard.pages.terms');
    }
}
