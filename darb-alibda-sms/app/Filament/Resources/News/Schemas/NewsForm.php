<?php

namespace App\Filament\Resources\News\Schemas;

use App\Enums\AudienceType;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('dashboard.labels.news_information'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('dashboard.labels.title'))
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.title_required'),
                                'max' => __('dashboard.validation.title_max'),
                            ])
                            ->maxLength(255),

                        Select::make('audience')
                            ->label(__('dashboard.labels.audience'))
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.audience_required'),
                            ])
                            ->options(AudienceType::options()),

                        Textarea::make('body')
                            ->label(__('dashboard.labels.body'))
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.body_required'),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('dashboard.labels.images'))
                    ->schema([

                        FileUpload::make('images')
                            ->label(__('dashboard.labels.images'))
                            ->multiple()
                            ->image()
                            ->maxSize(51200)
                            ->disk('public')
                            ->directory('news/images')
                            ->reorderable()
                            ->imagePreviewHeight('200')
                            ->panelLayout('grid'),

                    ]),

                Section::make(__('dashboard.labels.videos'))
                    ->schema([

                        FileUpload::make('videos')
                            ->label(__('dashboard.labels.videos'))
                            ->multiple()
                            ->maxSize(51200)
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/webm',
                                'video/quicktime',
                            ])
                            ->disk('public')
                            ->directory('news/videos')

                    ]),
            ]);
    }

}