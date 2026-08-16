<?php

namespace App\Filament\Resources\StudentEnrollments\Schemas;

use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentEnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                ->label(__('dashboard.labels.student'))
                ->relationship('student', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => $record->full_name
                )
                ->searchable()
                ->preload()
                ->required()
                ->validationMessages([
                    'required' => __('dashboard.validation.student_id_required'),
                ]),

            Select::make('section_id')
                ->label(__('dashboard.labels.section'))
                ->relationship('section', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => $record->full_name
                )
                ->searchable()
                ->preload()
                ->required()
                ->validationMessages([
                    'required' => __('dashboard.validation.section_id_required'),
                ]),

            TextInput::make('academic_year')
                ->label(__('dashboard.labels.academic_year'))
                ->required()
                ->maxLength(20)
                ->validationMessages([
                    'required' => __('dashboard.validation.academic_year_required'),
                    'max' => __('dashboard.validation.academic_year_max'),
                ]),

            DatePicker::make('enrollment_date')
                ->label(__('dashboard.labels.enrollment_date'))
                ->required()
                ->validationMessages([
                    'required' => __('dashboard.validation.enrollment_date_required'),
                ]),

            Select::make('status')
                ->label(__('dashboard.labels.status'))
                ->options(StudentStatus::options())
                ->required()
                ->validationMessages([
                    'required' => __('dashboard.validation.status_required'),
                ]),

            Select::make('final_result')
                ->label(__('dashboard.labels.final_result'))
                ->options(MarkResult::options())
                ->default('pending')
                ->disabled()
                ->required()
                ->validationMessages([
                    'required' => __('dashboard.validation.final_result_required'),
                ]),

            TextInput::make('final_average')
                ->label(__('dashboard.labels.final_average'))
                ->numeric()
                ->disabled()
                ->suffix('%')
                ->validationMessages([
                    'numeric' => __('dashboard.validation.final_average_numeric'),
                ]),
            

            Textarea::make('notes')
                ->label(__('dashboard.labels.notes'))
                ->columnSpanFull()
                ->validationMessages([
                    'max' => __('dashboard.validation.notes_max'),
                ])
            ]);
    }
}
