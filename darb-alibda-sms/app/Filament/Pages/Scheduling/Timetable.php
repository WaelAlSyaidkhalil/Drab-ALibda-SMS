<?php

namespace App\Filament\Pages\Scheduling;

use App\Enums\DayOfWeek;
use App\Models\Academic\Section;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Term;
use App\Models\Subjects\Subject;
use App\Models\Academic\Teacher;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Timetable extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.scheduling.timetable';

    protected static \UnitEnum|string|null $navigationGroup = 'Scheduling';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::TableCells;
    protected static ?string $navigationLabel = 'Timetable';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public ?int $termId = null;
    public ?int $sectionId = null;

    public array $grid = [];

    public ?string $selectedDay = null;
    public ?int $selectedSlotId = null;

    public function mount(): void
    {
        $this->form->fill();
        $this->grid = $this->buildEmptyGrid();
    }

    /* ================= FORM ================= */

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('termId')
                ->label('Term')
                ->options(Term::all()->mapWithKeys(fn ($t) => [
                    $t->id => $t->academicYearAndTerm,
                ]))
                ->searchable()
                ->live()
                ->afterStateUpdated(fn ($state) => tap($this, function () use ($state) {
                    $this->termId = $state;
                    $this->loadGrid();
                })),

            Select::make('sectionId')
                ->label('Section')
                ->options(
                    Section::query()->get()->mapWithKeys(fn ($s) => [
                        $s->id => $s->schoolClass->type->label() . ' - ' . $s->name,
                    ])
                )
                ->searchable()
                ->live()
                ->afterStateUpdated(fn ($state) => tap($this, function () use ($state) {
                    $this->sectionId = $state;
                    $this->loadGrid();
                })),
        ])
        ->columns(3)
        ->statePath('data');
    }

    /* ================= GRID ================= */

    public function loadGrid(): void
    {
        $this->grid = $this->buildEmptyGrid();

        if (! $this->termId || ! $this->sectionId) {
            return;
        }

        $schedules = Schedule::query()
            ->where('term_id', $this->termId)
            ->where('section_id', $this->sectionId)
            ->with(['subject', 'teacher'])
            ->get();

        foreach ($schedules as $schedule) {
            $this->grid[$schedule->day->value][$schedule->time_slot_id] = [
                'id' => $schedule->id,
                'subject' => $schedule->subject?->name,
                'teacher' => $schedule->teacher?->full_name,
                'subject_id' => $schedule->subject_id,
                'teacher_id' => $schedule->teacher_id,
            ];
        }
    }

    private function buildEmptyGrid(): array
    {
        $grid = [];

        foreach (DayOfWeek::cases() as $day) {
            foreach (TimeSlot::query()->pluck('id') as $slotId) {
                $grid[$day->value][$slotId] = null;
            }
        }

        return $grid;
    }

    public function getDays()
    {
        return DayOfWeek::cases();
    }

    public function getTimeSlots()
    {
        return TimeSlot::query()->orderBy('start_time')->get();
    }

    /* ================= CELL CLICK ================= */

    public function openCell(string $day, int $slotId): void
    {
        if (! $this->ensureContextSelected()) {
            $this->notifyMissingContext();
            return;
        }

        $this->selectedDay = $day;
        $this->selectedSlotId = $slotId;

        $schedule = Schedule::query()
            ->where('term_id', $this->termId)
            ->where('section_id', $this->sectionId)
            ->where('day', $day)
            ->where('time_slot_id', $slotId)
            ->first();

        $this->mountAction('editCell', [
            'subject_id' => $schedule?->subject_id,
            'teacher_id' => $schedule?->teacher_id,
        ]);
    }

    /* ================= ACTION (CREATE + EDIT) ================= */

    protected function getActions(): array
    {
        return [
            Action::make('editCell')
                ->label('Save Schedule')
                ->modalHeading('Schedule Editor')
                ->form([
                    Select::make('subject_id')
                        ->label('Subject')
                        ->options(Subject::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('teacher_id')
                        ->label('Teacher')
                        ->options(Teacher::all()->mapWithKeys(fn ($t) => [
                            $t->id => $t->full_name,
                        ]))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {

                    Schedule::updateOrCreate(
                        [
                            'term_id' => $this->termId,
                            'section_id' => $this->sectionId,
                            'day' => $this->selectedDay,
                            'time_slot_id' => $this->selectedSlotId,
                        ],
                        [
                            'subject_id' => $data['subject_id'],
                            'teacher_id' => $data['teacher_id'],
                        ]
                    );

                    $this->loadGrid();

                    $this->selectedDay = null;
                    $this->selectedSlotId = null;
                    
                })->hidden(fn() => !$this->selectedDay || !$this->selectedSlotId),
        ];
    }

    private function ensureContextSelected(): bool
    {
        return $this->termId && $this->sectionId;
    }

    private function notifyMissingContext(): void
    {
        Notification::make()
            ->title('Missing Selection')
            ->body('Please select Term and Section first.')
            ->danger()
            ->send();
    }
}