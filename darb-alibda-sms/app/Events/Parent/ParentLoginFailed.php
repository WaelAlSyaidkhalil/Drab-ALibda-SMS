<?php

namespace App\Events\Parent;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParentLoginFailed
{
    use Dispatchable, SerializesModels;

    public string $phone;
    public string $ip;
    public string $reason;

    public function __construct(
        string $phone,
        string $ip,
        string $reason = 'failed_login'
    ) {
        $this->phone = $phone;
        $this->ip = $ip;
        $this->reason = $reason;
    }
}