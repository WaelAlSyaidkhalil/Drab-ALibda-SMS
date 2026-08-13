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
        // Telescope is a dev-only dependency (not installed with --no-dev in
        // production), so only register its provider when the package exists.
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\Telescope::class)) {
            $this->app->register(\App\Providers\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AuthenticateMiddleware::redirectUsing(fn () => null);
        AuthenticationException::redirectUsing(fn () => null);
        AuthenticateSessionMiddleware::redirectUsing(fn () => null);

      //  Event::listen(TeacherLoggedIn::class, [SendLoginNotification::class, 'handle']);
      //  Event::listen(TeacherLoginFailed::class, [LogFailedLoginAttempt::class, 'handle']);
    }
}
