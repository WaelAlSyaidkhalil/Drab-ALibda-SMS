<?php

namespace App\Filament\Pages;

use App\Enums\DayOfWeek;
use App\Models\Academic\Teacher;
use App\Models\Schedule\TimeSlot;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TeacherTimetable extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.teacher-timetable';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::TableCells;

    protected static \UnitEnum|string|null $navigationGroup = 'Teacher Management';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Teacher Timetable';

    public ?array $data = [];

    public ?int $teacherId = null;

    public function mount(): void
    {
        $this->teacherId = Teacher::query()->value('id');

        $this->form->fill([
            'teacherId' => $this->teacherId,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacherId')
                    ->label('Teacher')
                    ->options(
                        Teacher::query()
                            ->get()
                            ->pluck('full_name', 'id')
                    )
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(
                        fn ($state) => $this->teacherId = $state
                    )
                    ->required(),
            ])
            ->statePath('data');
    }

    public function getDays(): array
    {
        return DayOfWeek::cases();
    }

    public function getTimeSlots()
    {
        return TimeSlot::query()
            ->orderBy('start_time')
            ->get();
    }

    public function getGrid(): array
    {
        $grid = [];

        foreach ($this->getDays() as $day) {
            foreach ($this->getTimeSlots() as $slot) {
                $grid[$day->value][$slot->id] = null;
            }
        }

        if (! $this->teacherId) {
            return $grid;
        }

        $teacher = Teacher::with([
            'schedules.subject',
            'schedules.section',
        ])->find($this->teacherId);

        foreach ($teacher?->schedules ?? [] as $schedule) {
            $grid[$schedule->day->value][$schedule->time_slot_id] = $schedule;
        }
        return $grid;
    }
}