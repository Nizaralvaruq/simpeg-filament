<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;

use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('IHYA')
            ->brandLogo(asset('images/logo1.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/logo1.png'))
            ->databaseNotifications(true)
            ->globalSearch(true)
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(true)
            ->profile(\App\Filament\Pages\EditProfile::class)
            ->font('Plus Jakarta Sans')
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverResources(in: base_path('Modules/Kepegawaian/app/Filament/Resources'), for: 'Modules\\Kepegawaian\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/MasterData/app/Filament/Resources'), for: 'Modules\\MasterData\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Presensi/app/Filament/Resources'), for: 'Modules\\Presensi\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Resign/app/Filament/Resources'), for: 'Modules\\Resign\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Leave/app/Filament/Resources'), for: 'Modules\\Leave\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/PenilaianKinerja/app/Filament/Resources'), for: 'Modules\\PenilaianKinerja\\Filament\\Resources')

            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverPages(in: base_path('Modules/Presensi/app/Filament/Pages'), for: 'Modules\\Presensi\\Filament\\Pages')
            ->discoverPages(in: base_path('Modules/MasterData/app/Filament/Pages'), for: 'Modules\\MasterData\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->discoverWidgets(in: base_path('Modules/Presensi/app/Filament/Widgets'), for: 'Modules\\Presensi\\Filament\\Widgets')

            ->widgets([])
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->navigationGroups([
                'Menu Saya',
                'Kepegawaian',
                'Presensi',
                'Penilaian Kinerja',
                'Data Master',
                'Authorization',
            ]);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn(): string => Blade::render('
                @if(auth()->check())
                    <div class="hidden md:flex items-center px-3 py-1 text-[10px] font-black uppercase rounded-lg mr-2 border {{ 
                            match(true) {
                                auth()->user()->hasRole(\'super_admin\') => \'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-800\',
                                auth()->user()->hasRole(\'ketua_psdm\') => \'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-800\',
                                auth()->user()->hasAnyRole([\'kepala_sekolah\', \'koor_jenjang\', \'admin_unit\']) => \'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-800\',
                                default => \'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-800\',
                            }
                        }}">
                        <span class="mr-1.5 flex h-2 w-2 rounded-full animate-pulse {{ 
                            match(true) {
                                auth()->user()->hasRole(\'super_admin\') => \'bg-rose-500\',
                                auth()->user()->hasRole(\'ketua_psdm\') => \'bg-emerald-500\',
                                auth()->user()->hasAnyRole([\'kepala_sekolah\', \'koor_jenjang\', \'admin_unit\']) => \'bg-sky-500\',
                                default => \'bg-slate-400\',
                            }
                        }}"></span>
                        {{ str_replace(\'_\', \' \', auth()->user()->roles->first()?->name ?? \'Staff\') }}
                    </div>
                @endif
            '),
        );
    }
}
