<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\FinancialReport;
use App\Filament\Admin\Pages\Settings\SettingsWorkspace;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Inject the global colorful theme into the entire admin panel.
        // Register directly AND via serving() to ensure it's present on
        // every panel request (including Livewire updates).
        Filament::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn () => view('filament.theme.global-theme'),
        );

        Filament::serving(function () {
            Filament::registerRenderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => view('filament.theme.global-theme'),
            );
        });
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->font('Inter')
            ->brandName('Accounting')
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                FinancialReport::class,
                SettingsWorkspace::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            // 📌 Sidebar Group Order (Enterprise Hierarchy)
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('Sales & CRM'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('Procurement'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('Inventory'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('Banking'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('Financials'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('Reports'))
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label(fn (): string => __('Settings'))
                    ->collapsed(false),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}