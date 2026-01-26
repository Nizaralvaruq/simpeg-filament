<x-filament-panels::page>
    <div x-data="locationHandler()" x-init="init()">
        <form wire:submit.prevent="submit" class="space-y-6">
            {{ $this->form }}

            <input type="hidden" wire:model="latitude">
            <input type="hidden" wire:model="longitude">
            <input type="hidden" wire:model="alamat_lokasi">

            <div class="mt-4">
                {{-- Kotak Status Dihapus sesuai permintaan --}}
            </div>

            {{-- Submit Action --}}
            <div
                class="flex flex-col items-center justify-center gap-4 pt-8 border-t border-gray-100 dark:border-white/5">
                <template x-if="!locationFound">
                    <p
                        class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-black text-center animate-pulse">
                        Menunggu Sinyal Lokasi...
                    </p>
                </template>

                <x-filament::button type="submit" size="xl" color="primary" icon="heroicon-o-paper-airplane"
                    ::disabled="!locationFound || loading" wire:loading.attr="disabled"
                    class="w-full sm:w-80 shadow-2xl transition-all active:scale-95 py-4">
                    <span wire:loading.remove>Simpan Kehadiran</span>
                    <span wire:loading>Memproses...</span>
                </x-filament::button>
            </div>
        </form>
    </div>

    @script
        <script>
            function locationHandler() {
                return {
                    loading: false,
                    locationFound: false,
                    error: false,
                    errorMessage: '',
                    coords: {
                        lat: null,
                        lng: null
                    },
                    init() {
                        this.getLocation();
                    },
                    getLocation() {
                        this.loading = true;
                        this.error = false;
                        this.locationFound = false;
                        if (!navigator.geolocation) {
                            this.loading = false;
                            this.error = true;
                            this.errorMessage = 'Fitur GPS tidak didukung.';
                            return;
                        }
                        navigator.geolocation.getCurrentPosition(
                            async (position) => {
                                    this.coords.lat = position.coords.latitude;
                                    this.coords.lng = position.coords.longitude;
                                    $wire.set('latitude', this.coords.lat);
                                    $wire.set('longitude', this.coords.lng);
                                    this.locationFound = true;
                                    this.loading = false;
                                    try {
                                        const response = await fetch(
                                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.coords.lat}&lon=${this.coords.lng}`
                                        );
                                        if (response.ok) {
                                            const data = await response.json();
                                            if (data?.display_name) {
                                                $wire.set('alamat_lokasi', data.display_name);
                                            }
                                        }
                                    } catch (e) {}
                                },
                                (err) => {
                                    this.loading = false;
                                    this.error = true;
                                    const msgs = {
                                        1: "Akses GPS ditolak",
                                        2: "Sinyal GPS tidak tersedia",
                                        3: "Waktu habis"
                                    };
                                    this.errorMessage = msgs[err.code] || `Error: ${err.message}`;
                                }, {
                                    enableHighAccuracy: true,
                                    timeout: 15000,
                                    maximumAge: 0
                                }
                        );
                    }
                }
            }
        </script>
    @endscript
</x-filament-panels::page>
