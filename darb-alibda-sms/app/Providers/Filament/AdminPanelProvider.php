<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LanguageSwitcher;
use App\Http\Middleware\Admin\LocalizationMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(asset('images/logo.jpeg'))
            ->brandLogoHeight('3rem')
            ->brandName(__('dashboard.app_name'))
            ->login()
            ->globalSearch(false)
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => view('filament.pages.language-switcher')->render(),
            )

            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => app()->getLocale() == 'ar'? 
                '<script>document.documentElement.dir = "rtl";</script>':
                '<script>document.documentElement.dir = "ltr";</script>'
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(__('dashboard.navigation.school_management')),

                NavigationGroup::make()
                    ->label(__('dashboard.navigation.student_management')),

                NavigationGroup::make()
                    ->label(__('dashboard.navigation.teacher_management')),

                NavigationGroup::make()
                    ->label(__('dashboard.navigation.scheduling')),

                NavigationGroup::make()
                    ->label(__('dashboard.navigation.communication')),

                NavigationGroup::make()
                    ->label(__('dashboard.navigation.feedback_center')),

                NavigationGroup::make()
                    ->label(__('dashboard.navigation.user_management')),

                NavigationGroup::make()
                    ->label(__('dashboard.navigation.system'))

            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                LocalizationMiddleware::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
