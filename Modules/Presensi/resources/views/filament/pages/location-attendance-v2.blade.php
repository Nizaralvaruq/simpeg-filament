<x-filament-panels::page>
    <div x-data="{
        lat: @entangle('latitude'),
        lng: @entangle('longitude'),
        detecting: true,
        error: null,
        init() {
            this.getLocation();
        },
        getLocation() {
            this.detecting = true;
            this.error = null;
            if (!navigator.geolocation) {
                this.error = 'Browser Anda tidak mendukung geolokasi.';
                this.detecting = false;
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude;
                    this.lng = pos.coords.longitude;
                    this.detecting = false;
                },
                (err) => {
                    this.error = 'Gagal mendeteksi lokasi: ' + err.message;
                    this.detecting = false;
                }, { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    }">
        <form wire:submit.prevent="submit" class="space-y-6">
            {{-- Status Lokasi --}}
            <div class="p-4 rounded-xl border flex items-center gap-4 transition-all shadow-sm"
                :class="{
                    'bg-primary-50 border-primary-300 dark:bg-primary-500/10 dark:border-primary-500/60': detecting,
                    'bg-success-50 border-success-300 dark:bg-success-500/10 dark:border-success-500/60': !detecting &&
                        lat && lng,
                    'bg-danger-50 border-danger-300 dark:bg-danger-500/10 dark:border-danger-500/60': !detecting && (!
                        lat || !lng)
                }">
                <div
                    class="w-12 h-12 flex items-center justify-center rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700">
                    <template x-if="detecting">
                        <x-filament::loading-indicator class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </template>
                    <template x-if="!detecting && lat && lng">
                        <x-filament::icon icon="heroicon-o-check-circle"
                            class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </template>
                    <template x-if="!detecting && (!lat || !lng)">
                        <x-filament::icon icon="heroicon-o-x-circle"
                            class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                    </template>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-950 dark:text-white"
                        x-text="detecting ? 'Mendeteksi Lokasi...' : (lat && lng ? 'Lokasi Terdeteksi' : 'Gagal Mendeteksi')">
                    </h4>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mt-0.5 font-mono"
                        x-text="detecting ? 'Mohon tunggu sebentar...' : (lat && lng ? `Koordinat: ${lat}, ${lng}` : (error || 'Mohon izinkan akses GPS browser.'))">
                    </p>
                </div>
                <template x-if="!detecting && (!lat || !lng)">
                    <x-filament::button @click="getLocation()" size="xs" color="gray"
                        icon="heroicon-o-arrow-path">
                        Coba Lagi
                    </x-filament::button>
                </template>
            </div>

            {{ $this->form }}

            {{-- Submit Action --}}
            <div
                class="flex flex-col items-center justify-center gap-4 pt-8 border-t border-gray-100 dark:border-white/5">
                <x-filament::button type="submit" size="xl" color="primary" icon="heroicon-o-paper-airplane"
                    ::disabled="detecting || !lat || !lng" class="w-full sm:w-80 shadow-2xl transition-all active:scale-95 py-4">
                    <span wire:loading.remove>Simpan Kehadiran</span>
                    <span wire:loading>Memproses...</span>
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
