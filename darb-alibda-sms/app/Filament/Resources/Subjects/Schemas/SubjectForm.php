<?php

namespace App\Filament\Resources\Subjects\Schemas;

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
                Section::make('Subject Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('full_mark')
                            ->numeric()
                            ->default(100)
                            ->minValue(1)
                            ->required(),

                        TextInput::make('pass_mark')
                            ->numeric()
                            ->default(50)
                            ->minValue(0)
                            ->required()
                            ->lte('full_mark'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
            ])->columns(1);
    }
}