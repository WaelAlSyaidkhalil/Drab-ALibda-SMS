<?php

namespace App\Filament\Resources\Communication\NewsResource\Forms;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('News Information')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Select::make('audience')
                            ->required()
                            ->options([
                                'all' => 'All',
                                'teachers' => 'Teachers',
                                'students' => 'Students',
                                'parents' => 'Parents',
                            ]),

                        RichEditor::make('body')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Images')
                    ->schema([

                        FileUpload::make('images')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('news/images')
                            ->reorderable()
                            ->imagePreviewHeight('200')
                            ->panelLayout('grid'),

                    ]),

                Section::make('Videos')
                    ->schema([

                        FileUpload::make('videos')
                            ->multiple()
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