<?php

namespace App\Filament\Resources\StudentEnrollments\RelationManagers;

use App\Enums\MarkResult;
use App\Enums\SubjectComponentType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MarksRelationManager extends RelationManager
{
    protected static string $relationship = 'studentMarks';

    protected static ?string $title = 'Subject Component Marks';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('dashboard.labels.subject_component_marks');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('subject_id')
                ->label(__('dashboard.labels.subject'))
                ->relationship('subject', 'name')
                ->searchable()
                ->preload()
                ->live() 
                ->required(),

            Select::make('subject_component_id')
                ->label(__('dashboard.labels.component'))
                ->options(function (Get $get) {
                    $subjectId = $get('subject_id');

                    if (! $subjectId) {
                        return [];
                    }

                    return \App\Models\Subjects\SubjectComponent::query()
                        ->where('subject_id', $subjectId)
                        ->get()
                        ->mapWithKeys(fn($component) => [
                            $component->id => $component->type->label(),
                        ]);
                })
                ->searchable()
                ->preload()
                ->required(),

            Select::make('term_id')
                ->label(__('dashboard.labels.term'))
                ->relationship('term', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn($record): string => $record->term_name ?? 'Unknown'
                )
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('mark')
                ->label(__('dashboard.labels.mark'))
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
                    ->label(__('dashboard.labels.subject'))
                    ->searchable(),

                TextColumn::make('subjectComponent.type')
                    ->label(__('dashboard.labels.component'))
                    ->formatStateUsing(fn($state) => $state->label())
                    ->colors(SubjectComponentType::getColors())
                    ->badge(),

                TextColumn::make('term.term_name')
                    ->label(__('dashboard.labels.term')),

                TextColumn::make('mark_display')
                    ->label(__('dashboard.labels.mark')),

                TextColumn::make('percentage_display')
                    ->label(__('dashboard.labels.percentage')),

            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->label(__('dashboard.labels.subject'))
                    ->relationship('subject', 'name'),

                SelectFilter::make('subject_component_id')
                    ->label(__('dashboard.labels.component'))
                    ->relationship('subjectComponent', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record): string => $record->type->label()),

                SelectFilter::make('term_id')
                    ->label(__('dashboard.labels.term'))
                    ->relationship('term', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn($record): string => $record->term_name ?? 'Unknown'
                    ),

                SelectFilter::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options(MarkResult::options()),
            ])
            ->headerActions([
                CreateAction::make()->label(__('dashboard.buttons.new_subject_component_mark'))->modalHeading(__('dashboard.buttons.new_subject_component_mark')),
            ])
            ->recordActions([
                EditAction::make()->modalHeading(__('dashboard.buttons.edit') . ' ' . __('dashboard.pages.subject_component_mark')),
                DeleteAction::make()->modalHeading(__('dashboard.buttons.delete') . ' ' . __('dashboard.pages.subject_component_mark')),
            ]);
    }
}
