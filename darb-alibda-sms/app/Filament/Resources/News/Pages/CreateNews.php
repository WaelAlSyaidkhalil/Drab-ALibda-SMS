<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Models\Communication\News;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected array $uploadedImages = [];

    protected array $uploadedVideos = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store uploaded files temporarily
        $this->uploadedImages = $data['images'] ?? [];
        $this->uploadedVideos = $data['videos'] ?? [];

        // Remove non-model fields
        unset($data['images'], $data['videos']);

        // Fill additional model data
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->storeAttachments(
            $this->record,
            $this->uploadedImages,
            'image'
        );

        $this->storeAttachments(
            $this->record,
            $this->uploadedVideos,
            'video'
        );
    }

    protected function storeAttachments(
        News $news,
        array $files,
        string $type
    ): void {

        foreach ($files as $order => $path) {

            $disk = 'public';

            $fullPath = Storage::disk($disk)->path($path);

            $news->attachments()->create([
                'attachable'    => 'news_id',
                'disk'          => $disk,
                'path'          => $path,
                'original_name' => basename($path),
                'mime_type'     => mime_content_type($fullPath),
                'size'          => filesize($fullPath),
                'type'          => $type,
                'order'         => $order,
                'created_by'    => auth()->id(),
            ]);
        }
    }
}