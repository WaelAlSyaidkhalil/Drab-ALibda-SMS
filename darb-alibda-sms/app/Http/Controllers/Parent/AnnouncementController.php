<?php

namespace App\Http\Controllers\Parent;

use App\Models\Communication\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends ParentController
{
    /**
     * قائمة الإعلانات المتاحة لأولياء الأمور
     */
    public function index(Request $request): JsonResponse
    {
        $announcements = News::query()
            ->whereIn('audience', ['all', 'parents'])
            ->with([
                'attachments' => function ($query) {
                    $query->where('type', 'image')
                        ->orderBy('order')
                        ->limit(1);
                }
            ])
            ->latest('created_at')
            ->paginate(15);

        $announcements->getCollection()->transform(function ($announcement) {
            return [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'description' => $announcement->body,

                'thumbnail' => $announcement->attachments->first()?->url,

                'published_at' => $announcement->created_at?->toDateTimeString(),
            ];
        });

        return $this->paginatedResponse(
            $announcements,
            'تم جلب الإعلانات بنجاح.'
        );
    }


    /**
     * تفاصيل إعلان معين
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $announcement = News::query()
            ->whereIn('audience', ['all', 'parents'])
            ->with([
                'attachments' => function ($query) {
                    $query->orderBy('order');
                }
            ])
            ->findOrFail($id);


        return $this->successResponse([

            'id' => $announcement->id,

            'title' => $announcement->title,

            'description' => $announcement->body,


            'attachments' => $announcement->attachments
                ->map(function ($attachment) {

                    return [
                        'id' => $attachment->id,

                        'url' => $attachment->url,

                        'name' => $attachment->original_name,

                        'type' => $attachment->type,

                        'mime_type' => $attachment->mime_type,

                        'size' => $attachment->size,
                    ];

                })
                ->values(),


            'published_at' => $announcement->created_at?->toDateTimeString(),


        ], 'تم جلب الإعلان بنجاح.');
    }
}