<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TeacherAttendanceCalendar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \UnitEnum|string|null $navigationGroup = 'Teacher Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Teacher Attendance Calendar';

    protected static ?int $navigationSort = 11;

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament.pages.teacher-attendance-calendar';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getTitle(): string
    {
        return __('dashboard.pages.teacher_attendance_calendar');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.teacher_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.teacher_attendance_calendar');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([]);
    }
}
