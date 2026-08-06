<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\ComplaintRepository;
use Illuminate\Support\Collection;

class ComplaintService
{
    public function __construct(
        protected ComplaintRepository $complaintRepository
    ) {
    }


    /**
     * جلب جميع شكاوى المستخدم.
     */
    public function getUserComplaints(int $userId): Collection
    {
        return $this->complaintRepository
            ->getUserComplaints($userId);
    }


    /**
     * جلب شكوى واحدة للمستخدم.
     */
    public function getUserComplaintById(
        int $userId,
        int $complaintId
    ): array {
        return $this->complaintRepository
            ->getUserComplaintById(
                $userId,
                $complaintId
            );
    }


    /**
     * إنشاء شكوى جديدة.
     */
    public function create(
        int $userId,
        array $data
    ): array {
        return $this->complaintRepository
            ->create(
                $userId,
                $data
            );
    }
}