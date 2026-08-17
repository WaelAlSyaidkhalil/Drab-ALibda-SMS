<?php

namespace App\Filament\Resources\AbsenceJustifications;

use App\Filament\Resources\AbsenceJustifications\Pages\ManageAbsenceJustifications;
use App\Models\Communication\AbsenceJustification;
use App\Notifications\Parent\TeacherActionNotification;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AbsenceJustificationResource extends Resource
{
    protected static ?string $model = AbsenceJustification::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::DocumentText;

    protected static ?int $navigationSort = 4;

    protected static \UnitEnum|string|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'تبرير الغياب';

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.communication');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.absence_justification');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->label(__('dashboard.labels.status'))
                ->options([
                    'pending' => __('dashboard.enums.absence_justification_status.pending'),
                    'approved' => __('dashboard.enums.absence_justification_status.approved'),
                    'rejected' => __('dashboard.enums.absence_justification_status.rejected'),
                ])
                ->required(),

            Textarea::make('review_note')
                ->label(__('dashboard.labels.review_note'))
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label(__('dashboard.labels.student'))
                    ->searchable(),

                TextColumn::make('parent.name')
                    ->label(__('dashboard.labels.parent_name'))
                    ->searchable(),

                TextColumn::make('absence_date')
                    ->label(__('dashboard.labels.absence_date'))
                    ->date(),

                TextColumn::make('reason')
                    ->label(__('dashboard.labels.reason'))
                    ->limit(60),

                TextColumn::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->badge()
                    ->colors([
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => __('dashboard.enums.absence_justification_status.pending'),
                        'approved' => __('dashboard.enums.absence_justification_status.approved'),
                        'rejected' => __('dashboard.enums.absence_justification_status.rejected'),
                        default => $state,
                    }),

                TextColumn::make('review_note')
                    ->label(__('dashboard.labels.review_note'))
                    ->limit(40)
                    ->placeholder(__('dashboard.labels.none')),

                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options([
                        'pending' => __('dashboard.enums.absence_justification_status.pending'),
                        'approved' => __('dashboard.enums.absence_justification_status.approved'),
                        'rejected' => __('dashboard.enums.absence_justification_status.rejected'),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('dashboard.buttons.update_status'))
                    ->modalHeading(__('dashboard.labels.update_absence_justification_status'))
                    ->using(function (AbsenceJustification $record, array $data): AbsenceJustification {
                        $record->update([
                            'status' => $data['status'],
                            'review_note' => $data['review_note'] ?? null,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        $parent = $record->parent ?? $record->student?->parent;
                        if ($parent) {
                            $parent->notifyNow(new \App\Notifications\Admin\AbsenceJustificationStatusUpdatedNotification(
                                $record,
                                auth()->user()
                            ));
                        }

                        return $record;
                    }),
                DeleteAction::make()
                    ->label(__('dashboard.buttons.delete'))
                    ->modalHeading(__('dashboard.labels.delete_absence_justification'))
                    ->before(function (AbsenceJustification $record) {
                        $record->attachments()->delete();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAbsenceJustifications::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.pages.absence_justification');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.pages.absence_justification');
    }
}
