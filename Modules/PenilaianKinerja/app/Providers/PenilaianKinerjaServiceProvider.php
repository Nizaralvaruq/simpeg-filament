<?php

namespace Modules\PenilaianKinerja\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class PenilaianKinerjaServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'PenilaianKinerja';

    protected string $nameLower = 'penilaiankinerja';

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        //
    }

    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\PenilaianKinerja\Console\SendAppraisalReminders::class,
        ]);
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}
