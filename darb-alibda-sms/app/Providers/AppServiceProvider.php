<?php

namespace App\Providers;

use App\Events\Teacher\TeacherLoggedIn;
use App\Events\Teacher\TeacherLoginFailed;
use App\Listeners\Teacher\LogFailedLoginAttempt;
use App\Listeners\Teacher\SendLoginNotification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Session\Middleware\AuthenticateSession as AuthenticateSessionMiddleware;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AuthenticateMiddleware::redirectUsing(fn () => null);
        AuthenticationException::redirectUsing(fn () => null);
        AuthenticateSessionMiddleware::redirectUsing(fn () => null);

        Event::listen(TeacherLoggedIn::class, [SendLoginNotification::class, 'handle']);
        Event::listen(TeacherLoginFailed::class, [LogFailedLoginAttempt::class, 'handle']);
    }
}
