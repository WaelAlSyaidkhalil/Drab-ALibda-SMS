<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('dashboard.labels.subject_details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('dashboard.labels.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label(__('dashboard.labels.code'))
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->required(),

                        Select::make('class_id')
                            ->label(__('dashboard.labels.class'))
                            ->relationship('schoolClass', 'type')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->type->label())
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('teacher_id')
                            ->label(__('dashboard.labels.teacher'))
                            ->relationship('teacher', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('full_mark')
                            ->label(__('dashboard.labels.full_mark'))
                            ->numeric()
                            ->default(100)
                            ->minValue(1)
                            ->required(),

                        TextInput::make('pass_mark')
                            ->label(__('dashboard.labels.pass_mark'))
                            ->numeric()
                            ->default(50)
                            ->minValue(0)
                            ->required()
                            ->lte('full_mark'),

                        Textarea::make('description')
                            ->label(__('dashboard.labels.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
            ])->columns(1);
    }
}