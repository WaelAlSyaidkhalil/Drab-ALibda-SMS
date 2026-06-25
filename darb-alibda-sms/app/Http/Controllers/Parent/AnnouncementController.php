<?php

namespace App\Http\Controllers\Parent;

use App\Models\Communication\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends ParentController
{
    public function index(Request $request): JsonResponse
    {
        $announcements = News::query()
            ->whereIn('audience', ['all', 'parents'])
            ->latest('created_at')
            ->paginate(15);

        return $this->paginatedResponse($announcements, 'تم جلب الإعلانات بنجاح.');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $announcement = News::query()->whereIn('audience', ['all', 'parents'])->findOrFail($id);

        return $this->successResponse([
            'title' => $announcement->title,
            'description' => $announcement->body,
            'image' => $announcement->attachments->first()?->url,
            'published_at' => $announcement->created_at?->toDateTimeString(),
        ], 'تم جلب الإعلان بنجاح.');
    }
}
