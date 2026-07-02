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

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.feedback_center');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.complaints');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options(ComplaintStatus::options())
                    ->required(),

                Textarea::make('response')
                    ->label(__('dashboard.labels.response'))
                    ->rows(6)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('dashboard.labels.title'))
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label(__('dashboard.labels.user'))
                    ->searchable(),

                TextColumn::make('body')
                    ->label(__('dashboard.labels.body'))
                    ->limit(50),

                TextColumn::make('response')
                    ->label(__('dashboard.labels.response'))
                    ->limit(50)
                    ->placeholder(__('dashboard.labels.not_available')),

                TextColumn::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(ComplaintStatus::getColors()),

                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->dateTime(),

                TextColumn::make('resolved_at')
                    ->label(__('dashboard.labels.resolved_at'))
                    ->dateTime()
                    ->placeholder(__('dashboard.labels.not_available')),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(__('dashboard.labels.update_complaint'))
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
                    ->label(__('dashboard.labels.status')),
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

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.labels.complaints');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.pages.complaint    ');
    }
}