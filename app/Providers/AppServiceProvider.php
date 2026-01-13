<?php

namespace App\Providers;

use Carbon\Carbon;
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
        // Set timezone to Indonesia (WIB)
        date_default_timezone_set('Asia/Jakarta');

        Carbon::setLocale('id');

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Register Kegiatan Policy
        \Illuminate\Support\Facades\Gate::policy(
            \Modules\Presensi\Models\Kegiatan::class,
            \Modules\Presensi\Policies\KegiatanPolicy::class
        );
    }
}
