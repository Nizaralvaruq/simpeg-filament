<x-filament-panels::page>
    @php
        $stats = $this->getStats();
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-6">
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-success-500/10 rounded-xl">
                    <x-filament::icon icon="heroicon-o-check-badge" class="w-8 h-8 text-success-600" />
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Kehadiran Bulan Ini</div>
                    <div class="text-2xl font-bold">{{ $stats['hadir'] }} Hari</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-danger-500/10 rounded-xl">
                    <x-filament::icon icon="heroicon-o-clock" class="w-8 h-8 text-danger-600" />
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Keterlambatan</div>
                    <div class="text-2xl font-bold">{{ $stats['late'] }} Menit</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-warning-500/10 rounded-xl">
                    <x-filament::icon icon="heroicon-o-face-frown" class="w-8 h-8 text-warning-600" />
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Izin / Sakit</div>
                    <div class="text-2xl font-bold">{{ $stats['izin_sakit'] }} Hari</div>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
