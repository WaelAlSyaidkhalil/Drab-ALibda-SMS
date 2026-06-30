<?php

namespace App\Filament\Resources\Suggestions;

use App\Enums\SuggestionStatus;
use App\Filament\Resources\Suggestions\Pages\ManageSuggestions;
use App\Models\Communication\Suggestion;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static \UnitEnum|string|null $navigationGroup = 'Feedback Center';

    protected static ?string $navigationLabel = 'Suggestions';

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.feedback_center');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.suggestions');
    }

    protected static ?int $navigationSort = 1;
    // ───── FORM (modal only) ─────
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->label(__('dashboard.labels.status'))
                ->options(SuggestionStatus::options())
                ->required(),

                Textarea::make('feedback')
                ->label(__('dashboard.labels.feedback'))
                ->rows(4)
                ->columnSpanFull(),

                Toggle::make('is_acknowledged')
                    ->label(__('dashboard.labels.acknowledged')),
        ]);
    }

    // ───── TABLE ─────
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('dashboard.labels.title'))
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label(__('dashboard.labels.user')),
                    
                TextColumn::make('body')
                    ->label(__('dashboard.labels.body'))
                    ->limit(50),
                    
                TextColumn::make('feedback')
                    ->label(__('dashboard.labels.feedback'))
                    ->limit(50)
                    ->placeholder(__('dashboard.labels.not_available')),

                TextColumn::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(SuggestionStatus::getColors()),

                IconColumn::make('is_acknowledged')
                    ->boolean()
                    ->label(__('dashboard.labels.acknowledged')),

                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->dateTime(),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(__('dashboard.labels.update_suggestion')),
            ])
            ->filters([
                Filter::make('acknowledged')
                    ->label(__('dashboard.labels.acknowledged'))
                    ->query(fn ($query) => $query->where('is_acknowledged', true)),

                Filter::make('not_acknowledged')
                    ->label(__('dashboard.labels.not_acknowledged'))
                    ->query(fn ($query) => $query->where('is_acknowledged', false)),

                SelectFilter::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options(SuggestionStatus::options())
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