<?php

namespace App\Providers\Filament;

use AzGasim\FilamentUnsavedChangesModal\FilamentUnsavedChangesModalPlugin;
use Cmsmaxinc\FilamentErrorPages\FilamentErrorPagesPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use YousefAman\ModalRepeater\ModalRepeaterPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->unsavedChangesAlerts()
            ->plugin(FilamentUnsavedChangesModalPlugin::make())
            ->plugin(ModalRepeaterPlugin::make())
            ->plugin(FilamentErrorPagesPlugin::make())
            ->login()
            ->brandName('Symbiosa')
            ->brandLogo(fn () => view('filament.brand.logo'))
            ->brandLogoHeight('2.25rem')
            ->font(
                'Synonym',
                url: 'https://api.fontshare.com/v2/css?f[]=synonym@300,400,500,600,700&f[]=chillax@400,500,600,700&display=swap',
                provider: GoogleFontProvider::class,
            )
            ->monoFont('JetBrains Mono')
            ->colors([
                'primary' => Color::hex('#51A687'),
                'gray' => self::grayPalette(),
                'purple' => Color::Purple,
            ])
            ->sidebarWidth('17rem')
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => Blade::render('<x-admin.sidebar-live-event />'),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => Blade::render('<x-admin.environment-badge />'),
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render('<x-admin.view-site-button />'),
            )
            ->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn (): string => Blade::render('<x-admin.auth-aside />'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
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

    /**
     * Neutral palette with a slight green bias, so greys sit with the brand colour
     * instead of looking like the default cool Filament grey.
     *
     * @return array<int, string>
     */
    protected static function grayPalette(): array
    {
        return [
            50 => 'oklch(0.985 0.003 165)',
            100 => 'oklch(0.967 0.005 165)',
            200 => 'oklch(0.922 0.008 165)',
            300 => 'oklch(0.87 0.011 165)',
            400 => 'oklch(0.705 0.015 165)',
            500 => 'oklch(0.556 0.017 165)',
            600 => 'oklch(0.446 0.017 165)',
            700 => 'oklch(0.373 0.016 165)',
            800 => 'oklch(0.269 0.014 165)',
            900 => 'oklch(0.2 0.012 165)',
            950 => 'oklch(0.145 0.01 165)',
        ];
    }
}
