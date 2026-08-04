<?php

namespace App\Repositories\Teacher;

use App\Enums\SuggestionStatus;
use App\Models\Communication\Suggestion;
use Illuminate\Support\Collection;

class SuggestionRepository
{
    /**
     * جلب جميع اقتراحات المستخدم.
     */
    public function getUserSuggestions(int $userId): Collection
    {
        return Suggestion::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn (Suggestion $suggestion) => $this->formatSuggestion($suggestion));
    }

    /**
     * جلب اقتراح واحد.
     */
    public function getUserSuggestionById(int $userId, int $suggestionId): array
    {
        $suggestion = Suggestion::where('user_id', $userId)
            ->findOrFail($suggestionId);

        return $this->formatSuggestion($suggestion);
    }

    /**
     * إنشاء اقتراح جديد.
     */
    public function create(int $userId, array $data): array
    {
        $suggestion = Suggestion::create([
            'user_id' => $userId,
            'title' => $data['title'],
            'body' => $data['body'],
            'status' => SuggestionStatus::Pending,
            'is_acknowledged' => false,
            'feedback' => null,
        ]);

        return $this->formatSuggestion($suggestion);
    }

    /**
     * تهيئة بيانات الاقتراح لإرسالها إلى Flutter.
     */
    private function formatSuggestion(Suggestion $suggestion): array
    {
        return [
            'id' => $suggestion->id,

            'title' => $suggestion->title,

            'body' => $suggestion->body,

            'status' => $suggestion->status->value,

            'status_label' => $suggestion->status->label(),

            'feedback' => $suggestion->feedback,

            'is_acknowledged' => $suggestion->is_acknowledged,

            'created_at' => $suggestion->created_at?->toDateTimeString(),

            'updated_at' => $suggestion->updated_at?->toDateTimeString(),
        ];
    }
}