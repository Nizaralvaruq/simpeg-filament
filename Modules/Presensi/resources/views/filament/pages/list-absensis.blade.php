<x-filament-panels::page>
    <x-filament::tabs class="mb-4">
        <x-filament::tabs.item
            :active="$activeTab === 'log'"
            wire:click="$set('activeTab', 'log')"
            icon="heroicon-m-clock"
        >
            Log Harian
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$activeTab === 'kegiatan'"
            wire:click="$set('activeTab', 'kegiatan')"
            icon="heroicon-m-calendar-days"
        >
            Absen Kegiatan
        </x-filament::tabs.item>

        @if (auth()->user() && !auth()->user()->hasRole('staff'))
            <x-filament::tabs.item
                :active="$activeTab === 'rekap'"
                wire:click="$set('activeTab', 'rekap')"
                icon="heroicon-m-chart-bar"
            >
                Rekap Bulanan
            </x-filament::tabs.item>
        @endif
    </x-filament::tabs>

    @if ($activeTab === 'log')
        {{ $this->table }}
    @elseif ($activeTab === 'kegiatan')
        @livewire(\Modules\Presensi\Filament\Widgets\AbsenKegiatanWidget::class)
    @elseif ($activeTab === 'rekap')
        @livewire(\Modules\Presensi\Filament\Widgets\RekapAbsensiWidget::class)
    @endif
</x-filament-panels::page>
