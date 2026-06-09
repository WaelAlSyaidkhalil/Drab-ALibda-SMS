<?php

namespace App\Events\Teacher;

use App\Models\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeacherLoggedIn
{
    use Dispatchable, SerializesModels;

    public User $user;
    public string $ip;

    public function __construct(User $user, string $ip)
    {
        $this->user = $user;
        $this->ip = $ip;
    }
}
