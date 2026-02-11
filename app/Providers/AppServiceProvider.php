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
        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setLocale('id');

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Modular Policy Registration
        \Illuminate\Support\Facades\Gate::policy(
            \Modules\Kepegawaian\Models\DataInduk::class,
            \Modules\Kepegawaian\Policies\DataIndukPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\Presensi\Models\Absensi::class,
            \Modules\Presensi\Policies\AbsensiPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\Presensi\Models\JadwalPiket::class,
            \Modules\Presensi\Policies\JadwalPiketPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\Presensi\Models\Kegiatan::class,
            \Modules\Presensi\Policies\KegiatanPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\Leave\Models\LeaveRequest::class,
            \Modules\Leave\Policies\LeaveRequestPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\PenilaianKinerja\Models\AppraisalAssignment::class,
            \Modules\PenilaianKinerja\Policies\AppraisalAssignmentPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\PenilaianKinerja\Models\AppraisalCategory::class,
            \Modules\PenilaianKinerja\Policies\AppraisalCategoryPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\PenilaianKinerja\Models\AppraisalSession::class,
            \Modules\PenilaianKinerja\Policies\AppraisalSessionPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\PenilaianKinerja\Models\PerformanceScore::class,
            \Modules\PenilaianKinerja\Policies\PerformanceScorePolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\Resign\Models\Resign::class,
            \Modules\Resign\Policies\ResignPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\MasterData\Models\Unit::class,
            \Modules\MasterData\Policies\UnitPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \Modules\MasterData\Models\Golongan::class,
            \Modules\MasterData\Policies\GolonganPolicy::class
        );

        \Illuminate\Support\Facades\Gate::policy(
            \App\Models\User::class,
            \App\Policies\UserPolicy::class
        );
    }
}
