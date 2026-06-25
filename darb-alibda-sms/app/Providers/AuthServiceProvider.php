<?php

namespace App\Providers;

use App\Models\Academic\Student;
use App\Policies\Parent\StudentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Student::class => StudentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
