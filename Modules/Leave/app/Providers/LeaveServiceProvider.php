<?php

namespace Modules\Leave\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Leave\Models\LeaveRequest;
use Modules\Leave\Observers\LeaveRequestObserver;

class LeaveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LeaveRequest::observe(LeaveRequestObserver::class);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
