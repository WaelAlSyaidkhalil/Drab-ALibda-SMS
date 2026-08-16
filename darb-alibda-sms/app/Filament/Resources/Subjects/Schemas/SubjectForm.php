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
                            ->validationMessages([
                                'required' => __('dashboard.validation.name_required'),
                                'max' => __('dashboard.validation.name_max'),
                            ])
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label(__('dashboard.labels.code'))
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.code_required'),
                                'unique' => __('dashboard.validation.code_unique'),
                            ]),

                        Select::make('class_id')
                            ->label(__('dashboard.labels.class'))
                            ->relationship('schoolClass', 'type')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->type->label())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.class_id_required'),
                            ]),

                        Select::make('teacher_id')
                            ->label(__('dashboard.labels.teacher'))
                            ->relationship('teacher', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.teacher_id_required'),
                            ]),

                        TextInput::make('full_mark')
                            ->label(__('dashboard.labels.full_mark'))
                            ->numeric()
                            ->default(100)
                            ->minValue(1)
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.full_mark_required'),
                                'numeric' => __('dashboard.validation.full_mark_numeric'),
                                'min' => __('dashboard.validation.full_mark_min'),
                            ]),

                        TextInput::make('pass_mark')
                            ->label(__('dashboard.labels.pass_mark'))
                            ->numeric()
                            ->default(50)
                            ->minValue(0)
                            ->required()
                            ->lte('full_mark')
                            ->validationMessages([
                                'required' => __('dashboard.validation.pass_mark_required'),
                                'numeric' => __('dashboard.validation.pass_mark_numeric'),
                                'min' => __('dashboard.validation.pass_mark_min'),
                                'lte' => __('dashboard.validation.pass_mark_lte'),
                            ]),

                        Textarea::make('description')
                            ->label(__('dashboard.labels.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
            ])->columns(1);
    }
}