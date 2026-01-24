<x-filament-panels::page>
    @php
        $stats = $this->getStats();
    @endphp

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
        {{-- Stat: Kehadiran --}}
        <div
            class="relative group overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all duration-300 hover:shadow-md">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <x-filament::icon icon="heroicon-o-check-badge" class="w-24 h-24 text-success-600" />
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-4 bg-success-50 dark:bg-success-500/10 rounded-xl">
                    <x-filament::icon icon="heroicon-o-calendar" class="w-8 h-8 text-success-600 dark:text-success-500" />
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">
                        Kehadiran
                        (Bulan Ini)</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['hadir'] }} <span
                            class="text-sm font-medium text-gray-400">Hari</span></p>
                </div>
            </div>
        </div>

        {{-- Stat: Keterlambatan --}}
        <div
            class="relative group overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all duration-300 hover:shadow-md">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <x-filament::icon icon="heroicon-o-clock" class="w-24 h-24 text-danger-600" />
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-4 bg-danger-50 dark:bg-danger-500/10 rounded-xl">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle"
                        class="w-8 h-8 text-danger-600 dark:text-danger-500" />
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Total
                        Keterlambatan</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['late'] }} <span
                            class="text-sm font-medium text-gray-400">Menit</span></p>
                </div>
            </div>
        </div>

        {{-- Stat: Izin / Sakit --}}
        <div
            class="relative group overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all duration-300 hover:shadow-md">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <x-filament::icon icon="heroicon-o-face-frown" class="w-24 h-24 text-warning-600" />
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-4 bg-warning-50 dark:bg-warning-500/10 rounded-xl">
                    <x-filament::icon icon="heroicon-o-briefcase"
                        class="w-8 h-8 text-warning-600 dark:text-warning-500" />
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Izin /
                        Sakit</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['izin_sakit'] }} <span
                            class="text-sm font-medium text-gray-400">Hari</span></p>
                </div>
            </div>
        </div>
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
