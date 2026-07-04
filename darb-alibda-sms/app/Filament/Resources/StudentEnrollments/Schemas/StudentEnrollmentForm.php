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
                    'required' => __('validation.custom.student_id.required'),
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
                    'required' => __('validation.custom.section_id.required'),
                ]),

            TextInput::make('academic_year')
                ->label(__('dashboard.labels.academic_year'))
                ->required()
                ->maxLength(20)
                ->validationMessages([
                    'required' => __('validation.custom.academic_year.required'),
                    'max' => __('validation.custom.academic_year.max'),
                ]),

            DatePicker::make('enrollment_date')
                ->label(__('dashboard.labels.enrollment_date'))
                ->required()
                ->validationMessages([
                    'required' => __('validation.custom.enrollment_date.required'),
                ]),

            Select::make('status')
                ->label(__('dashboard.labels.status'))
                ->options(StudentStatus::options())
                ->required()
                ->validationMessages([
                    'required' => __('validation.custom.status.required'),
                ]),

            Select::make('final_result')
                ->label(__('dashboard.labels.final_result'))
                ->options(MarkResult::options())
                ->default('pending')
                ->disabled()
                ->required()
                ->validationMessages([
                    'required' => __('validation.custom.final_result.required'),
                ]),

            TextInput::make('final_average')
                ->label(__('dashboard.labels.final_average'))
                ->numeric()
                ->disabled()
                ->suffix('%')
                ->validationMessages([
                    'numeric' => __('validation.custom.final_average.numeric'),
                ]),
            

            Textarea::make('notes')
                ->label(__('dashboard.labels.notes'))
                ->columnSpanFull()
                ->validationMessages([
                    'max' => __('validation.custom.notes.max'),
                ])
            ]);
    }
}
