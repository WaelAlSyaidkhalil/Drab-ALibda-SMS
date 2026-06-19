<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Enums\MarkResult;
use App\Enums\SubjectComponentType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MarksRelationManager extends RelationManager
{
    protected static string $relationship = 'studentMarks';

    protected static ?string $title = 'Subject Component Marks';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('subject_component_id')
                ->relationship('subjectComponent', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn ($record): string => $record->type?->getArabic() ?? 'Unknown'
                )
                ->searchable()
                ->preload()
                ->required(),

            Select::make('term_id')
                ->relationship('term', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn ($record): string => $record->term_name ?? 'Unknown'
                )
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('mark')
                ->numeric()
                ->required()
                ->minValue(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject.name')
                    ->searchable(),

                TextColumn::make('subjectComponent.type')
                    ->formatStateUsing(
                        fn ($state) =>
                            $state instanceof SubjectComponentType
                                ? $state->getArabic()
                                : SubjectComponentType::tryFrom($state)?->getArabic() ?? 'Unknown'
                    ),

                TextColumn::make('term.term_name'),

                TextColumn::make('mark_display')
                ->label('Mark'),

                TextColumn::make('percentage_display')
                ->label('Percentage'),

            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name'),

                SelectFilter::make('subject_component_id')
                    ->label('Component')
                    ->relationship('subjectComponent', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record): string => $record->type?->getArabic() ?? 'Unknown'
                    ),

                SelectFilter::make('term_id')
                    ->label('Term')
                    ->relationship('term', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record): string => $record->term_name ?? 'Unknown'
                    ),

                SelectFilter::make('status')
                    ->options([
                        'pass' => 'ناجح',
                        'fail' => 'راسب',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}