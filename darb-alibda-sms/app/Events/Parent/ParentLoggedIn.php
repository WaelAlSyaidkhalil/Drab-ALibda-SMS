<?php

namespace App\Events\Parent;

use App\Models\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParentLoggedIn
{
    use Dispatchable, SerializesModels;

    public User $user;
    public string $ip;

    public function __construct(
        User $user,
        string $ip
    ) {
        $this->user = $user;
        $this->ip = $ip;
    }
}