<?php

namespace App\Filament\Pages;

use App\Enums\DayOfWeek;
use App\Models\Academic\Teacher;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Term;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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
        $defaultTeacherId = Teacher::query()->value('id');
        $defaultTermId = Term::query()->value('id');

        $this->teacherId = $defaultTeacherId;

        $this->form->fill([
            'teacherId' => $defaultTeacherId,
            'term_id' => $defaultTermId,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('teacherId')
                ->label('Teacher')
                ->options(
                    Teacher::query()
                        ->get()
                        ->mapWithKeys(fn ($teacher) => [
                            $teacher->id => $teacher->getFullNameAttribute(),
                        ])
                )
                ->searchable()
                ->live()
                ->afterStateUpdated(fn ($state) => $this->teacherId = $state)
                ->required(),

            Select::make('term_id')
                ->label('Term')
                ->options(
                    Term::query()
                        ->get()
                        ->mapWithKeys(fn ($term) => [
                            $term->id => $term->getAcademicYearAndTermAttribute(),
                        ])
                )
                ->searchable()
                ->live()
                ->afterStateUpdated(fn ($state) => $this->data['term_id'] = $state)
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

        $termId = $this->data['term_id'] ?? null;

        if (! $this->teacherId || ! $termId) {
            return $grid;
        }

        $schedules = Schedule::forTeacher($this->teacherId)
            ->where('term_id', $termId)
            ->with(['subject', 'section.schoolClass'])
            ->get();

        foreach ($schedules as $schedule) {
            $dayKey = $schedule->day->value ?? $schedule->day;
            $grid[$dayKey][$schedule->time_slot_id] = $schedule;
        }

        return $grid;
    }
}