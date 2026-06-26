<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Models\Communication\News;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected array $uploadedImages = [];

    protected array $uploadedVideos = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['images'] = $this->record
            ->attachments()
            ->where('type', 'image')
            ->orderBy('order')
            ->pluck('path')
            ->toArray();

        $data['videos'] = $this->record
            ->attachments()
            ->where('type', 'video')
            ->orderBy('order')
            ->pluck('path')
            ->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->uploadedImages = $data['images'] ?? [];
        $this->uploadedVideos = $data['videos'] ?? [];

        unset($data['images'], $data['videos']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncAttachments(
            $this->record,
            $this->uploadedImages,
            'image'
        );

        $this->syncAttachments(
            $this->record,
            $this->uploadedVideos,
            'video'
        );
    }

    protected function syncAttachments(
        News $news,
        array $paths,
        string $type,
    ): void {

        $existing = $news->attachments()
            ->where('type', $type)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Delete removed attachments
        |--------------------------------------------------------------------------
        */

        foreach ($existing as $attachment) {

            if (! in_array($attachment->path, $paths)) {

                Storage::disk($attachment->disk)
                    ->delete($attachment->path);

                $attachment->delete();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create new attachments
        |--------------------------------------------------------------------------
        */

        foreach ($paths as $order => $path) {

            $attachment = $existing
                ->firstWhere('path', $path);

            if ($attachment) {

                $attachment->update([
                    'order' => $order,
                ]);

                continue;
            }

            $disk = 'public';

            $fullPath = Storage::disk($disk)->path($path);

            $news->attachments()->create([
                'disk' => $disk,
                'path' => $path,
                'original_name' => basename($path),
                'mime_type' => mime_content_type($fullPath),
                'size' => filesize($fullPath),
                'type' => $type,
                'order' => $order,
                'created_by' => auth()->id(),
            ]);
        }
    }
}