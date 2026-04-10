<?php

namespace Modules\PenilaianKinerja\Filament\Widgets;

use Filament\Widgets\Widget;

class PenilaianStatsWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('penilaiankinerja::components.stats-header');
    }
}
