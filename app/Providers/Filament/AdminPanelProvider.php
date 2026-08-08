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
        // Inject the global theme into the entire admin panel.
        // It is driven by CSS custom properties so changing the theme
        // color in Settings instantly repaints the sidebar, icons, buttons,
        // headings and accents across every panel request.
        $themeData = $this->resolveTheme();

        Filament::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn () => view('filament.theme.global-theme', $themeData),
        );

        Filament::serving(function () use ($themeData) {
            Filament::registerRenderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => view('filament.theme.global-theme', $themeData),
            );
        });
    }

    /**
     * Resolve the active theme (primary color + accent) from Settings.
     * Falls back to a branded indigo/purple palette if settings are unavailable.
     *
     * @return array{primary:string, accent:string, sidebarBg:string}
     */
    protected function resolveTheme(): array
    {
        $primary = '#6366f1'; // indigo
        $accent = '#a855f7';  // purple
        $sidebarBg = '#ffffff';

        try {
            $general = app(\App\Settings\GeneralSettings::class);
            $company = app(\App\Settings\CompanySettings::class);

            $hex = $company->company_primary_color ?? $general->company_primary_color ?? $primary;
            if (is_string($hex) && preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
                $primary = $hex;
            }

            // Explicit accent color from settings if provided, else derive it.
            $configAccent = $general->theme_accent_color ?? null;
            if (is_string($configAccent) && preg_match('/^#[0-9a-fA-F]{6}$/', $configAccent)) {
                $accent = $configAccent;
            } else {
                $accent = $this->deriveAccent($primary);
            }

            // Explicit sidebar background from settings if provided.
            $configSidebar = $general->theme_sidebar_color ?? null;
            if (is_string($configSidebar) && preg_match('/^#[0-9a-fA-F]{6}$/', $configSidebar)) {
                $sidebarBg = $configSidebar;
            } else {
                // Theme presets also map to a sidebar background.
                $mode = $general->application_theme ?? 'default';
                if (in_array($mode, ['dark', 'dark_sidebar'], true)) {
                    $sidebarBg = '#10112a';
                }
            }
        } catch (\Throwable $e) {
            // settings unavailable - fall back to defaults
        }

        return [
            'primary' => $primary,
            'accent' => $accent,
            'sidebarBg' => $sidebarBg,
        ];
    }

    /**
     * Derive a complementary accent color by rotating the hue of the primary.
     */
    protected function deriveAccent(string $hex): string
    {
        [$r, $g, $b] = array_map('hexdec', str_split(ltrim($hex, '#'), 2));
        $hsl = $this->rgbToHsl($r, $g, $b);
        $hsl['h'] = ($hsl['h'] + 40) % 360;
        $rgb = $this->hslToRgb($hsl['h'], $hsl['s'], max(0.5, $hsl['l']));
        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }

    protected function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255; $g /= 255; $b /= 255;
        $max = max($r, $g, $b); $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $h = $s = 0;
        if ($max !== $min) {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
                case $g: $h = ($b - $r) / $d + 2; break;
                default: $h = ($r - $g) / $d + 4;
            }
            $h /= 6;
        }
        return ['h' => $h * 360, 's' => $s, 'l' => $l];
    }

    protected function hslToRgb(float $h, float $s, float $l): array
    {
        $h /= 360;
        $r = $g = $b = $l;
        if ($s !== 0) {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->hue2rgb($p, $q, $h + 1 / 3);
            $g = $this->hue2rgb($p, $q, $h);
            $b = $this->hue2rgb($p, $q, $h - 1 / 3);
        }
        return [round($r * 255), round($g * 255), round($b * 255)];
    }

    protected function hue2rgb(float $p, float $q, float $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2) return $q;
        if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }

public function panel(Panel $panel): Panel
    {
        // Read company settings so changes take effect across the panel.
        $companyName = 'Accounting';
        $primaryColor = Color::Indigo;
        try {
            $company = app(\App\Settings\CompanySettings::class);
            if ($company->company_name) {
                $companyName = $company->company_name;
            }
            $hex = $company->company_primary_color ?? null;
            if ($hex && preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
                $primaryColor = \Filament\Support\Colors\Color::hex($hex);
            }
        } catch (\Throwable $e) {
            // settings table may not be migrated/seeded yet - fall back to defaults
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => $primaryColor,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->font('Inter')
            ->brandName($companyName)
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
            // Groups start collapsed; accordion JS auto-collapses others when one expands.
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('Sales & CRM'))
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label(fn (): string => __('Procurement'))
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label(fn (): string => __('Inventory'))
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label(fn (): string => __('Banking'))
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label(fn (): string => __('Financials'))
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label(fn (): string => __('Reports'))
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label(fn (): string => __('Settings'))
                    ->collapsed(true),
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