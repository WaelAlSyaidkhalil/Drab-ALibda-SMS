<?php

namespace App\Filament\Resources\News\Tables;

use App\Enums\AudienceType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('body')
                    ->formatStateUsing(fn ($state) => strip_tags($state))
                    ->limit(50),
                TextColumn::make('audience')
                    ->badge()
                    ->colors([
                        'success' => AudienceType::ALL,
                        'warning' => AudienceType::STUDENTS,
                        'info' => AudienceType::TEACHERS,
                        'danger' => AudienceType::PARENTS,
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->filters([
                SelectFilter::make('audience')
                    ->label('Audience')
                    ->options([
                        'teachers' => AudienceType::TEACHERS->value,
                        'parents' => AudienceType::PARENTS->value,
                        'students' => AudienceType::STUDENTS->value,
                    ])
                    ->placeholder('All audiences')
            ]);
    }
}