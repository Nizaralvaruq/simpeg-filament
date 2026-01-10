<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    {{ __('Presensi Harian') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->translatedFormat('l, d F Y') }} - <span id="clock" class="font-mono font-bold"></span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if (!$todayAttendance)
                <x-filament::button
                    wire:click="checkIn"
                    color="primary"
                    icon="heroicon-m-play"
                    size="lg">
                    Check-In Sekarang
                </x-filament::button>
                @elseif (!$todayAttendance->jam_keluar)
                <div class="flex items-center gap-4 mr-4 text-right">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Masuk Jam</p>
                        <p class="font-bold text-primary-600">{{ \Carbon\Carbon::parse($todayAttendance->jam_masuk)->format('H:i') }}</p>
                    </div>
                </div>
                <x-filament::button
                    wire:click="checkOut"
                    color="warning"
                    icon="heroicon-m-stop"
                    size="lg"
                    tooltip="Bisa check-out setelah jam 12:00">
                    Check-Out
                </x-filament::button>
                @else
                <div class="flex items-center gap-6 p-3 rounded-lg bg-success-50 dark:bg-success-950">
                    <div class="text-center">
                        <p class="text-xs text-success-600 uppercase">Masuk</p>
                        <p class="font-bold text-success-700">{{ \Carbon\Carbon::parse($todayAttendance->jam_masuk)->format('H:i') }}</p>
                    </div>
                    <div class="w-px h-8 bg-success-200 dark:bg-success-800"></div>
                    <div class="text-center">
                        <p class="text-xs text-success-600 uppercase">Pulang</p>
                        <p class="font-bold text-success-700">{{ \Carbon\Carbon::parse($todayAttendance->jam_keluar)->format('H:i') }}</p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-success-500">
                        <x-filament::icon
                            icon="heroicon-m-check"
                            class="w-6 h-6 text-white" />
                    </div>
                </div>
                @endif
            </div>
        </div>
    </x-filament::section>

    <script>
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('clock');
            if (clock) {
                clock.innerText = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</x-filament-widgets::widget>