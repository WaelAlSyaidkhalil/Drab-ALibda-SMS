<?php

namespace App\Filament\Resources\News\Tables;

use App\Enums\AudienceType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
            return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('dashboard.labels.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('body')
                    ->label(__('dashboard.labels.body'))
                    ->formatStateUsing(fn ($state) => strip_tags($state))
                    ->limit(50),
                TextColumn::make('audience')
                    ->label(__('dashboard.labels.audience'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(AudienceType::getColors()),

                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('readers_count')
                    ->label(__('dashboard.labels.readers_count'))
                    ->counts('readers')
                    ->label(__('dashboard.labels.readers'))
                    ->sortable()
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->modalHeading(__('dashboard.buttons.delete') . ' ' . __('dashboard.pages.news'))
            ])
            ->filters([
                SelectFilter::make('audience')
                    ->label(__('dashboard.labels.audience'))
                    ->options(AudienceType::options())
                    ->placeholder(__('dashboard.labels.all_audiences'))
            ]);
    }
}