<?php

namespace App\Filament\Resources\StudentEnrollments\RelationManagers;

use App\Enums\MarkResult;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentSubjectResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentSubjectResults';

    protected static ?string $title = 'Subject Results';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->unique(),

            TextInput::make('term1_mark')
                ->numeric()
                ->minValue(0)
                ->maxValue(100),

            TextInput::make('term2_mark')
                ->numeric()
                ->minValue(0)
                ->maxValue(100),

            TextInput::make('yearly_mark')
                ->numeric()
                ->disabled()
                ->minValue(0)
                ->maxValue(100),

            Select::make('result')
                ->disabled()
                ->options(MarkResult::options())
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject.name')
                    ->searchable(),

                TextColumn::make('term1_mark'),

                TextColumn::make('term2_mark'),

                TextColumn::make('yearly_mark_display')
                ->label('Yearly mark'),

                TextColumn::make('result')
                    ->badge()
                    ->formatStateUsing(
                        fn (MarkResult|string|null $state) =>
                            ($state instanceof MarkResult ? $state : MarkResult::tryFrom($state))
                    )
                    ->colors([
                        'primary' => MarkResult::PENDING,
                        'success' => MarkResult::PASS,
                        'danger' => MarkResult::FAIL,
                    ]),
            ])
            ->headerActions([
                CreateAction::make()->label('New subject result'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}