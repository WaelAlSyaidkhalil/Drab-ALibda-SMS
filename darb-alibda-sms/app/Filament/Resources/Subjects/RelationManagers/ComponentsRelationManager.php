<?php

namespace App\Filament\Resources\Subjects\RelationManagers;

use App\Enums\ComponentType;
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
                    ->label('نوع المكون')
                    ->options(SubjectComponentType::getWithArabic())
                    ->required()
                    ->native(false),

                TextInput::make('out_of')
                    ->label('الدرجة العليا')
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
                    ->label('الترتيب')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),

                Textarea::make('description')
                    ->label('الوصف')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                TextColumn::make('order')
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('out_of')
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable()
                    ->placeholder('N/A'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}