<?php

namespace App\Filament\Resources\TeacherAttendances\Schemas;

use App\Enums\AttendanceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TeacherAttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('teacher_id')
                    ->label(__('dashboard.labels.teacher'))
                    ->relationship('teacher', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => __('dashboard.validation.teacher_id_required'),
                    ]),

                Select::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options(AttendanceStatus::options())
                    ->required()
                    ->validationMessages([
                        'required' => __('dashboard.validation.status_required'),
                    ]),

                DatePicker::make('date')
                    ->label(__('dashboard.labels.date'))
                    ->required()
                    ->validationMessages([
                        'required' => __('dashboard.validation.date_required'),
                    ]),
            ]);
    }
}
