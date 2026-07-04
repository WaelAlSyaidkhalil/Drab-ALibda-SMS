<?php

namespace App\Filament\Resources\SchoolClasses\RelationManagers;

use App\Models\Academic\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Override;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    public function isReadOnly(): bool
    {
        // Ensure actions (edit/delete/create) are available when this relation manager is rendered
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label(__('dashboard.labels.name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1)
                    ->validationMessages([
                        'required' => __('validation.custom.name.required'),
                        'max' => __('validation.custom.name.max'),
                    ]),
                TextInput::make('capacity')
                    ->label(__('dashboard.labels.capacity'))
                    ->numeric()
                    ->required()
                    ->columnSpan(1)
                    ->validationMessages([
                        'required' => __('validation.custom.capacity.required'),
                    ]),
            ]);
    }

    public function table(Tables\Table $table): Table
    {
        return $table
            ->heading(__('dashboard.pages.sections'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('dashboard.labels.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->label(__('dashboard.labels.capacity'))
                    ->sortable(),
                TextColumn::make('student_count')
                    ->label(__('dashboard.labels.student_count'))
                    ->getStateUsing(fn (Section $record) => $record->student_count),
            ])
            ->headerActions([
                CreateAction::make()->modalHeading(__('dashboard.buttons.create_section'))->label(__('dashboard.buttons.create_section'))
            ])
            ->actions([
                EditAction::make()->modalHeading(fn($record) =>__('dashboard.buttons.edit') . ' ' . __('dashboard.pages.section') . ' ' . $record->name),
                DeleteAction::make()->modalHeading(fn($record) =>__('dashboard.buttons.delete') . ' ' . __('dashboard.pages.section') . ' ' . $record->name),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.pages.section');
    }
}
