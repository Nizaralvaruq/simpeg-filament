<x-filament-panels::page>
    <form wire:submit.prevent="create" class="space-y-6">
        {{ $this->form }}

        <x-filament::actions :actions="$this->getFormActions()" />
    </form>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                const componentId = '{{ $this->getId() }}';

                window.addEventListener('get-current-location', function() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const lat = position.coords.latitude;
                                const lng = position.coords.longitude;

                                // Find the current Livewire component by ID
                                const component = Livewire.find(componentId);

                                if (component) {
                                    component.set('data.latitude', lat);
                                    component.set('data.longitude', lng);

                                    new FilamentNotification()
                                        .title('Lokasi Berhasil Diambil')
                                        .success()
                                        .body(`Koordinat: ${lat}, ${lng}`)
                                        .send();
                                }
                            },
                            (error) => {
                                let msg = 'Gagal mengambil lokasi.';
                                switch (error.code) {
                                    case error.PERMISSION_DENIED:
                                        msg = 'Izin lokasi ditolak.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        msg = 'Posisi tidak tersedia.';
                                        break;
                                    case error.TIMEOUT:
                                        msg = 'Waktu habis (Timeout).';
                                        break;
                                }

                                new FilamentNotification()
                                    .title('Gagal')
                                    .danger()
                                    .body(msg)
                                    .send();
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    }
                });
            });
        </script>
    @endpush
</x-filament-panels::page>
