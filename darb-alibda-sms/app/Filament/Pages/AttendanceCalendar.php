<?php

namespace App\Filament\Pages;

use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AttendanceCalendar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \UnitEnum|string|null $navigationGroup = 'Student Management';
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Attendance Calendar';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament.pages.attendance-calendar';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                
                // =========================
                // CLASS SELECT (ENUM SAFE)
                // =========================
                Select::make('class_id')
                    ->label('Class')
                    ->options(fn () => SchoolClass::query()
                        ->get()
                        ->mapWithKeys(fn ($class) => [
                            $class->id => $class->type instanceof \BackedEnum
                                ? $class->type->label()   // ✅ safe enum handling
                                : (string) $class->type,
                        ])
                        ->toArray()
                    )
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('section_id', null))
                    ->required(),

                // =========================
                // SECTION SELECT (DEPENDENT)
                // =========================
                Select::make('section_id')
                    ->label('Section')
                    ->options(fn (callable $get) =>
                        $get('class_id')
                            ? Section::query()
                                ->where('class_id', $get('class_id'))
                                ->pluck('name', 'id') // or 'type' if string
                                ->toArray()
                            : []
                    )
                    ->live()
                    ->required(),
            ]);
    }
}