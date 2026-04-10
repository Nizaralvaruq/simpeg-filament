<?php

namespace Modules\Inventory\Filament\Widgets;

use Filament\Widgets\Widget;

/**
 * Widget pembungkus untuk menampilkan Livewire component statistik
 * inventaris di area header resource list, tanpa menggantikan
 * tombol-tombol aksi standar Filament.
 */
class InventoryStatsOverview extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('inventory::livewire.inventory-stats-overview');
    }
}
