<?php

namespace Modules\Kepegawaian\Filament\Widgets;

use Filament\Widgets\Widget;

/**
 * Widget pembungkus untuk menampilkan statistik
 * Data Pegawai di area header resource list.
 */
class KepegawaianStatsOverview extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('kepegawaian::livewire.kepegawaian-stats-overview');
    }
}
