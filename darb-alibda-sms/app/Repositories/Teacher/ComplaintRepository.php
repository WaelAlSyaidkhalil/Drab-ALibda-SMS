<?php

namespace App\Repositories\Teacher;

use App\Enums\ComplaintStatus;
use App\Models\Communication\Complaint;
use Illuminate\Support\Collection;

class ComplaintRepository
{
    /**
     * جلب جميع شكاوى المستخدم.
     */
    public function getUserComplaints(int $userId): Collection
    {
        return Complaint::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn (Complaint $complaint) => $this->formatComplaint($complaint));
    }

    /**
     * جلب شكوى واحدة.
     */
    public function getUserComplaintById(int $userId, int $complaintId): array
    {
        $complaint = Complaint::where('user_id', $userId)
            ->findOrFail($complaintId);

        return $this->formatComplaint($complaint);
    }

    /**
     * إنشاء شكوى جديدة.
     */
    public function create(int $userId, array $data): array
    {
        $complaint = Complaint::create([
            'user_id' => $userId,
            'title' => $data['title'],
            'body' => $data['body'],
            'status' => ComplaintStatus::PENDING,
            'response' => null,
            'resolved_at' => null,
        ]);

        return $this->formatComplaint($complaint);
    }

    /**
     * تجهيز بيانات الشكوى لـ Flutter.
     */
    private function formatComplaint(Complaint $complaint): array
    {
        return [
            'id' => $complaint->id,

            'title' => $complaint->title,

            'body' => $complaint->body,

            'status' => $complaint->status->value,

            'status_label' => $complaint->status->label(),

            'response' => $complaint->response,

            'resolved_at' => $complaint->resolved_at?->toDateTimeString(),

            'created_at' => $complaint->created_at?->toDateTimeString(),

            'updated_at' => $complaint->updated_at?->toDateTimeString(),
        ];
    }
}