<?php

namespace Modules\PenilaianKinerja\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class PenilaianKinerjaServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'PenilaianKinerja';

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        //
    }
}
