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
                                'required' => __('validation.custom.name.required'),
                                'max' => __('validation.custom.name.max'),
                            ])
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label(__('dashboard.labels.code'))
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.code.required'),
                                'unique' => __('validation.custom.code.unique'),
                            ]),

                        Select::make('class_id')
                            ->label(__('dashboard.labels.class'))
                            ->relationship('schoolClass', 'type')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->type->label())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.class_id.required'),
                            ]),

                        Select::make('teacher_id')
                            ->label(__('dashboard.labels.teacher'))
                            ->relationship('teacher', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.teacher_id.required'),
                            ]),

                        TextInput::make('full_mark')
                            ->label(__('dashboard.labels.full_mark'))
                            ->numeric()
                            ->default(100)
                            ->minValue(1)
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.full_mark.required'),
                                'numeric' => __('validation.custom.full_mark.numeric'),
                                'min' => __('validation.custom.full_mark.min'),
                            ]),

                        TextInput::make('pass_mark')
                            ->label(__('dashboard.labels.pass_mark'))
                            ->numeric()
                            ->default(50)
                            ->minValue(0)
                            ->required()
                            ->lte('full_mark')
                            ->validationMessages([
                                'required' => __('validation.custom.pass_mark.required'),
                                'numeric' => __('validation.custom.pass_mark.numeric'),
                                'min' => __('validation.custom.pass_mark.min'),
                                'lte' => __('validation.custom.pass_mark.lte'),
                            ]),

                        Textarea::make('description')
                            ->label(__('dashboard.labels.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
            ])->columns(1);
    }
}