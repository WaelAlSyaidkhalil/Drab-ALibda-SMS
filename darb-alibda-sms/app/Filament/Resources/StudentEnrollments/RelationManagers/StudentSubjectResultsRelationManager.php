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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class StudentSubjectResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentSubjectResults';

    protected static ?string $title = 'Subject Results';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('dashboard.labels.subject_results');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('subject_id')
                ->label(__('dashboard.labels.subject'))
                ->relationship('subject', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->unique(modifyRuleUsing: fn (Unique $rule) => $rule
                    ->where(
                        'enrollment_id',
                        $this->getOwnerRecord()->id
                    )
                    ->ignore($this->getMountedTableActionRecord())),

            TextInput::make('term1_mark')
                ->label(__('dashboard.labels.term1_mark'))
                ->numeric()
                ->minValue(0)
                ->maxValue(100),

            TextInput::make('term2_mark')
                ->label(__('dashboard.labels.term2_mark'))
                ->numeric()
                ->minValue(0)
                ->maxValue(100),

            TextInput::make('yearly_mark')
                ->label(__('dashboard.labels.yearly_mark'))
                ->numeric()
                ->disabled()
                ->minValue(0)
                ->maxValue(100),

            Select::make('result')
                ->label(__('dashboard.labels.result'))
                ->disabled()
                ->options(MarkResult::options())
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject.name')
                    ->label(__('dashboard.labels.subject'))
                    ->searchable(),

                TextColumn::make('term1_mark')
                    ->label(__('dashboard.labels.term1_mark')),

                TextColumn::make('term2_mark')
                    ->label(__('dashboard.labels.term2_mark')),

                TextColumn::make('yearly_mark_display')
                    ->label(__('dashboard.labels.yearly_mark')),

                TextColumn::make('result')
                    ->label(__('dashboard.labels.result'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->colors(MarkResult::getColors()),
            ])
            ->headerActions([
                CreateAction::make()->label(__('dashboard.buttons.new_subject_result'))->modalHeading(__('dashboard.buttons.new_subject_result')),
            ])
            ->recordActions([
                EditAction::make()->modalHeading(__('dashboard.buttons.edit') . ' ' . __('dashboard.pages.subject_result')),
                DeleteAction::make()->modalHeading(__('dashboard.buttons.delete') . ' ' . __('dashboard.pages.subject_result')),
            ]);
    }
}