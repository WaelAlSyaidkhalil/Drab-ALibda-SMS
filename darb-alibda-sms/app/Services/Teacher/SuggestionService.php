<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\SuggestionRepository;

class SuggestionService
{
    public function __construct(
        protected SuggestionRepository $suggestionRepository
    ) {
    }

    /**
     * جميع الاقتراحات.
     */
    public function index(int $userId)
    {
        return $this->suggestionRepository
            ->getUserSuggestions($userId);
    }

    /**
     * اقتراح واحد.
     */
    public function show(int $userId, int $suggestionId)
    {
        return $this->suggestionRepository
            ->getUserSuggestionById($userId, $suggestionId);
    }

    /**
     * إنشاء اقتراح.
     */
    public function store(int $userId, array $data)
    {
        return $this->suggestionRepository
            ->create($userId, $data);
    }
}