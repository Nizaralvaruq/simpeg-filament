<?php

namespace Modules\Retirement\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class RetirementServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Retirement';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        //
    }
}
