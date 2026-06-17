<?php

namespace App\Filament\Pages;

use App\Models\Communication\SchoolInfo;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolSettings extends Page implements HasForms
{
    use InteractsWithForms;


    protected static ?string $title = 'School Info';

    protected static \UnitEnum|string|null $navigationGroup = 'School Management';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.school-info';

    protected ?SchoolInfo $record = null;

    public ?array $data = [];

    protected function getListeners(): array
    {
        return [
            'refreshPage' => '$refresh',
        ];
    }

    public function mount(): void
    {
        $this->record = SchoolInfo::firstOrCreate([]);

        $this->form->fill([
            'name' => $this->record->name,
            'phone' => $this->record->phone,
            'email' => $this->record->email,
            'address' => $this->record->address,
            'website' => $this->record->website,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                        TextInput::make('name')
                            ->required(),

                        TextInput::make('phone'),

                        TextInput::make('email')
                            ->email(),

                        TextInput::make('website')
                            ->url(),

                        Textarea::make('address')
                            ->rows(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $this->record ??= SchoolInfo::firstOrCreate([]);

        $this->record->update($state);

        Notification::make()
            ->title('تم حفظ البيانات بنجاح')
            ->success()
            ->send();
    }
}
