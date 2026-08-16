<?php

namespace App\Providers;

use App\Events\Teacher\TeacherLoggedIn;
use App\Events\Teacher\TeacherLoginFailed;
use App\Jobs\SendFirebaseNotificationJob;
use App\Listeners\Teacher\LogFailedLoginAttempt;
use App\Listeners\Teacher\SendLoginNotification;
use App\Notifications\Admin\AdminFeedbackNotification;
use App\Notifications\Parent\TeacherActionNotification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Notifications\Events\NotificationSent;
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
        AuthenticateMiddleware::redirectUsing(fn() => null);
        AuthenticationException::redirectUsing(fn() => null);
        AuthenticateSessionMiddleware::redirectUsing(fn() => null);

        //  Event::listen(TeacherLoggedIn::class, [SendLoginNotification::class, 'handle']);
        //  Event::listen(TeacherLoginFailed::class, [LogFailedLoginAttempt::class, 'handle']);

        Event::listen(NotificationSent::class, function (NotificationSent $event): void {
            if (
                ! $event->notification instanceof TeacherActionNotification
                && ! $event->notification instanceof AdminFeedbackNotification
            ) {
                return;
            }

            if ($event->channel !== 'database') {
                return;
            }

            if (! isset($event->notifiable->fcm_token) || empty($event->notifiable->fcm_token)) {
                return;
            }

            SendFirebaseNotificationJob::dispatch(
                [$event->notifiable->fcm_token],
                $event->notification->getFirebaseTitle(),
                $event->notification->getFirebaseBody()
            );
        });

        // Register Attendance observer to create notifications on absence/late
        \App\Models\Schedule\Attendance::observe(\App\Observers\AttendanceObserver::class);
    }
}
