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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Sistem Kepegawaian')
            ->brandLogo(asset('images/logo1.png'))
            ->brandLogoHeight('3rem')
            ->databaseNotifications(false)
            ->globalSearch(false)
            ->profile(\App\Filament\Pages\EditProfile::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverResources(in: base_path('Modules/Kepegawaian/app/Filament/Resources'), for: 'Modules\\Kepegawaian\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/MasterData/app/Filament/Resources'), for: 'Modules\\MasterData\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Presensi/app/Filament/Resources'), for: 'Modules\\Presensi\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Resign/app/Filament/Resources'), for: 'Modules\\Resign\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Leave/app/Filament/Resources'), for: 'Modules\\Leave\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/PenilaianKinerja/app/Filament/Resources'), for: 'Modules\\PenilaianKinerja\\Filament\\Resources')



            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')

            ->widgets([

                \App\Filament\Widgets\GenderStatsOverview::class,
                \App\Filament\Widgets\HRStatsOverview::class,
                \App\Filament\Widgets\EmployeeDistributionChart::class,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ]);
    }
}
