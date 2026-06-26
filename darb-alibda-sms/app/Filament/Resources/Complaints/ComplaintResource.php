<?php

namespace App\Filament\Resources\Complaints;

use App\Enums\ComplaintStatus;
use App\Filament\Resources\Complaints\Pages\ManageComplaints;
use App\Models\Communication\Complaint;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Override;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static ?int $navigationSort = 2;

    protected static \UnitEnum|string|null $navigationGroup = 'Feedback Center';

    protected static ?string $navigationLabel = 'Complaints';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options(ComplaintStatus::options())
                    ->required(),

                Textarea::make('response')
                    ->rows(6)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),

                TextColumn::make('body')
                    ->limit(50),

                TextColumn::make('response')
                    ->limit(50)
                    ->placeholder('N/A'),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state instanceof ComplaintStatus ? $state : ComplaintStatus::tryFrom($state))
                    ->badge()
                    ->colors([
                        'success' => ComplaintStatus::RESOLVED,
                        'warning' => ComplaintStatus::PENDING,
                        'info' => ComplaintStatus::IN_PROGRESS,
                    ]),

                TextColumn::make('created_at')
                    ->dateTime(),

                TextColumn::make('resolved_at')
                    ->dateTime()
                    ->placeholder('N/A'),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Update Complaint')
                    ->using(function (Complaint $record, array $data): Complaint {
                        if (
                            $data['status'] === ComplaintStatus::RESOLVED->value &&
                            $record->resolved_at === null
                        ) {
                            $data['resolved_at'] = now();
                        }

                        if ($data['status'] !== ComplaintStatus::RESOLVED->value) {
                            $data['resolved_at'] = null;
                        }

                        $record->update($data);

                        return $record;
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ComplaintStatus::options())
                    ->label('Status'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageComplaints::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}