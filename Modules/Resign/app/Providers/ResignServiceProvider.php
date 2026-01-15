<?php

namespace Modules\Resign\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class ResignServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Resign';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        //
    }
}
