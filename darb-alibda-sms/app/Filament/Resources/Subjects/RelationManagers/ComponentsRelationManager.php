<?php

namespace App\Filament\Resources\Subjects\RelationManagers;

use App\Enums\SubjectComponentType;
use Closure;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'components';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('type')
                    ->label(__('dashboard.labels.component_type'))
                    ->options(SubjectComponentType::options())
                    ->required()
                    ->native(false),

                TextInput::make('out_of')
                    ->label(__('dashboard.labels.out_of'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->rules([
                        function (): Closure {
                            return function (string $attribute, $value, Closure $fail) {
                                $subject = $this->getOwnerRecord();

                                $currentRecordId = $this->getMountedTableActionRecord()?->id;

                                $currentSum = $subject->components()
                                    ->when(
                                        $currentRecordId,
                                        fn ($query) => $query->whereKeyNot($currentRecordId)
                                    )
                                    ->sum('out_of');

                                if (($currentSum + $value) > $subject->full_mark) {
                                    $fail(
                                        "مجموع درجات المكونات لا يمكن أن يتجاوز العلامة الكاملة للمادة ({$subject->full_mark})."
                                    );
                                }
                            };
                        },
                    ]),

                TextInput::make('order')
                    ->label(__('dashboard.labels.order'))
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),

                Textarea::make('description')
                    ->label(__('dashboard.labels.description'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading( __('dashboard.pages.subject_components'))
            ->defaultSort('order')
            ->columns([
                TextColumn::make('order')
                    ->label(__('dashboard.labels.order'))
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('dashboard.labels.component_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('out_of')
                    ->label(__('dashboard.labels.out_of'))
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('dashboard.labels.description'))
                    ->limit(50)
                    ->toggleable()
                    ->placeholder(__('dashboard.labels.not_available')),
            ])
            ->headerActions([
                CreateAction::make()->label(__('dashboard.buttons.create_subject_component'))->modalHeading(__('dashboard.buttons.create_subject_component')),
            ])
            ->actions([
                EditAction::make()->modalHeading(__('dashboard.buttons.edit_subject_component')),
                DeleteAction::make()->modalHeading(__('dashboard.buttons.delete') . ' ' . __('dashboard.pages.subject_component')),
            ]);
    }

}