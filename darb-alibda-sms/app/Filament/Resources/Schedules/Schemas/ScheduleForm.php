<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Enums\DayOfWeek;
use App\Models\Schedule\Schedule;
use App\Models\Subjects\Subject;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('term_id')
                    ->label(__('dashboard.labels.term'))
                    ->relationship('term', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->academic_year_and_term
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => __('dashboard.validation.term_id_required'),
                    ]),

                Select::make('section_id')
                    ->label(__('dashboard.labels.section'))
                    ->relationship('section', 'name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->unique(
                        table: 'schedules',
                        column: 'section_id',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                            ->where('term_id', $get('term_id'))
                            ->where('day', $get('day'))
                            ->where('time_slot_id', $get('time_slot_id'))
                    )
                    ->validationMessages([
                        'required' => __('dashboard.validation.section_id_required'),
                        'unique' => __('dashboard.validation.section_id_unique'),
                    ]),

                Select::make('day')
                    ->label(__('dashboard.labels.day'))
                    ->options(DayOfWeek::options())
                    ->required()
                    ->validationMessages([
                        'required' => __('dashboard.validation.day_required'),
                    ]),

                Select::make('time_slot_id')
                    ->label(__('dashboard.labels.time_slot'))
                    ->relationship('timeSlot', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    ),
                
                Select::make('subject_id')
                    ->label(__('dashboard.labels.subject'))
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->rules([
                        function (Get $get) {
                            return function (string $attribute, mixed $value, Closure $fail) use ($get) {
                                if (! $value || ! $get('term_id') || ! $get('day') || ! $get('time_slot_id')) {
                                    return;
                                }

                                $teacherId = Subject::query()
                                    ->whereKey($value)
                                    ->value('teacher_id');

                                if (! $teacherId) {
                                    return;
                                }

                                $exists = Schedule::query()
                                    ->where('term_id', $get('term_id'))
                                    ->where('day', $get('day'))
                                    ->where('time_slot_id', $get('time_slot_id'))
                                    ->whereHas('subject', fn ($query) => $query->where('teacher_id', $teacherId))
                                    ->exists();

                                if ($exists) {
                                    $fail(__('dashboard.validation.teacher_time_conflict'));
                                }
                            };
                        },
                    ])
                    ->afterStateUpdated(function ($state, callable $set) {
                        $teacherId = Subject::query()
                            ->whereKey($state)
                            ->value('teacher_id');

                        $set('teacher_id', $teacherId);
                    })
                    ->validationMessages([
                        'required' => __('dashboard.validation.subject_id_required'),
                    ]),

                Hidden::make('teacher_id')
                    ->default(null),

            ])
            ->columns(2);
    }
}