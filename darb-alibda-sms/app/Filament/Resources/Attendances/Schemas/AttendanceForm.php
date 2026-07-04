<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Enums\AttendanceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('student_id')
                    ->label(__('dashboard.labels.student'))
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.student_id.required'),
                    ]),

                Select::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options(AttendanceStatus::options())
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.status.required'),
                    ]),

                DatePicker::make('date')
                    ->label(__('dashboard.labels.date'))
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.date.required'),
                    ]),

                Textarea::make('reason')
                    ->label(__('dashboard.labels.reason'))
                    ->columnSpanFull()
                    ->maxLength(500)
                    ->validationMessages([
                        'max' => __('validation.custom.reason.max'),
                    ])
                    ->placeholder(__('dashboard.labels.optional')),
            ]);
        }
}
