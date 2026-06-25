<?php

namespace App\Filament\Resources\Suggestions;

use App\Filament\Resources\Communication\SuggestionResource\Pages;
use App\Filament\Resources\Suggestions\Pages\ManageSuggestions;
use App\Models\Communication\Suggestion;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static \UnitEnum|string|null $navigationGroup = 'Feedback Center';

    protected static ?string $navigationLabel = 'Suggestions';

    protected static ?int $navigationSort = 1;
    // ───── FORM (modal only) ─────
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('is_acknowledged')
                ->label('Acknowledged'),

            Textarea::make('feedback')
                ->label('Feedback')
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    // ───── TABLE ─────
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User'),
                    
                TextColumn::make('body')
                    ->limit(50),
                    
                TextColumn::make('feedback')
                    ->limit(50)
                    ->placeholder('N/A'),

                IconColumn::make('is_acknowledged')
                    ->boolean()
                    ->label('Acknowledged'),

                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Update Suggestion'),
            ])
            ->filters([
                Filter::make('acknowledged')
                    ->label('Acknowledged')
                    ->query(fn ($query) => $query->where('is_acknowledged', true)),

                Filter::make('not_acknowledged')
                    ->label('Not Acknowledged')
                    ->query(fn ($query) => $query->where('is_acknowledged', false)),
            ]);
    }

    // ───── ONLY INDEX PAGE ─────
    public static function getPages(): array
    {
        return [
            'index' => ManageSuggestions::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}