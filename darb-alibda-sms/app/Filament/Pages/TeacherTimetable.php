<?php

namespace App\Filament\Pages;

use App\Enums\DayOfWeek;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Teacher;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Subject;
use App\Models\Subjects\Term;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TeacherTimetable extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected string $view = 'filament.pages.teacher-timetable';
    protected static BackedEnum|string|null $navigationIcon = Heroicon::TableCells;
    protected static \UnitEnum|string|null $navigationGroup = 'Teacher Management';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationLabel = 'Teacher Timetable';

    public function getTitle(): string
    {
        return __('dashboard.pages.teacher_timetable');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.teacher_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.teacher_timetable');
    }
    public ?array $data = [];
    public array $grid = [];
    public ?int $teacherId = null;
    public ?int $termId = null;
    public ?string $selectedDay = null;
    public ?int $selectedSlotId = null;
    

    public function mount(): void
    {
        $this->form->fill();
        $this->grid = $this->buildEmptyGrid();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('teacherId')
                ->label(__('dashboard.labels.teacher'))
                ->options(
                    Teacher::query()
                        ->get()
                        ->mapWithKeys(fn ($teacher) => [
                            $teacher->id => $teacher->getFullNameAttribute(),
                        ])
                )
                ->searchable()
                ->live()
                ->afterStateUpdated(fn ($state) => tap($this, function () use ($state) {
                    $this->teacherId = $state;
                    $this->loadGrid();
                }))
                ->required(),

            Select::make('term_id')
                ->label(__('dashboard.labels.term'))
                ->options(
                    Term::query()
                        ->get()
                        ->mapWithKeys(fn ($term) => [
                            $term->id => $term->getAcademicYearAndTermAttribute(),
                        ])
                )
                ->searchable()
                ->live()
                ->afterStateUpdated(fn ($state) => tap($this, function () use ($state) {
                    $this->termId = $state;
                    $this->loadGrid();
                }))
                ->required(),
        ])
        ->columns(3)
        ->statePath('data');
    }


    /* ================= GRID ================= */

    public function loadGrid(): void
    {
        $this->grid = $this->buildEmptyGrid();

        if (! $this->termId || ! $this->teacherId) {
            return;
        }

        $schedules = Schedule::query()
            ->where('term_id', $this->termId)
            ->whereHas('subject', function ($query) {
                $query->where('teacher_id', $this->teacherId);
            })
            ->with(['subject', 'section'])
            ->get();

        foreach ($schedules as $schedule) {
            $this->grid[$schedule->day->value][$schedule->time_slot_id] = [
                'id' => $schedule->id,
                'subject' => $schedule->subject?->name,
                'section' => $schedule->section?->full_name,
                'subject_id' => $schedule->subject_id,
                'section_id' => $schedule->section_id,
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

        $schedules = Schedule::query()
            ->where('term_id', $this->termId)
            ->whereHas('subject', function ($query) {
                $query->where('teacher_id', $this->teacherId);
            })
            ->with(['subject', 'section'])
            ->get();

        foreach ($schedules as $schedule) {
            $dayKey = $schedule->day->value ?? $schedule->day;
            $grid[$dayKey][$schedule->time_slot_id] = $schedule;
        }

        return $grid;
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
            ->whereHas('subject', function ($query) {
                $query->where('teacher_id', $this->teacherId);
            })
            ->where('day', $day)
            ->where('time_slot_id', $slotId)
            ->first();

        $this->mountAction('editCell', [
            'subject_id' => $schedule?->subject_id,
            'section_id' => $schedule?->section_id,
        ]);
    }

    /* ================= ACTION (CREATE + EDIT) ================= */

    protected function getActions(): array
    {
        return [
            Action::make('editCell')
                ->label(__('dashboard.labels.save_schedule'))
                ->modalHeading(__('dashboard.labels.schedule_editor'))
                ->schema([
                    Select::make('section_id')
                        ->label(__('dashboard.labels.section'))
                        ->options(Section::whereHas('schoolClass', function ($classQuery) {
                            $classQuery->whereHas('subjects', function ($subjectQuery) {
                                $subjectQuery->where('teacher_id', $this->teacherId);
                            });
                        })->get()->mapWithKeys(fn ($section) => [
                            $section->id => $section->full_name,
                        ]))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {

                    Schedule::updateOrCreate(
                        [
                            'term_id' => $this->termId,
                            'day' => $this->selectedDay,
                            'time_slot_id' => $this->selectedSlotId,
                        ],
                        [
                            'subject_id' => Subject::where('teacher_id', $this->teacherId)->where('class_id', Section::find($data['section_id'])->class_id)->value('id'),
                            'section_id' => $data['section_id'],
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
        return $this->termId && $this->teacherId;
    }

    private function notifyMissingContext(): void
    {
        Notification::make()
            ->title(__('dashboard.labels.missing_selection'))
            ->body(__('dashboard.labels.missing_selection_message'))
            ->danger()
            ->send();
    }

      public function generateTimetableUsingORTools()
    {

        // Call the OR-Tools scheduling service to generate the timetable
        $success = app('App\Services\Admin\ORToolsSchedulerService')->generateTimetable();

        if ($success) {
            Notification::make()
                ->title(__('dashboard.labels.timetable_generated'))
                ->success()
                ->send();

            $this->loadGrid();
        } else {
            Notification::make()
                ->title(__('dashboard.labels.timetable_generation_failed'))
                ->danger()
                ->send();
        }
    }

}