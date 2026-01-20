<x-filament-panels::page>
    <form>
        {{ $this->form }}
    </form>

    <script>
        window.addEventListener('get-current-location', function() {
            if (navigator.geolocation) {
                // Show a loading notification or indicator if desired
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        // Use Livewire's $set to update the data state properly
                        @this.set('data.office_latitude', lat);
                        @this.set('data.office_longitude', lng);

                        // Dispatch event for Filament Notification
                        window.dispatchEvent(new CustomEvent('notification', {
                            detail: {
                                status: 'success',
                                title: 'Lokasi Berhasil Diambil',
                                body: `Titik koordinat berhasil diperbarui ke: ${lat}, ${lng}`
                            }
                        }));
                    },
                    (error) => {
                        let msg = 'Gagal mengambil lokasi.';
                        if (error.code === 1) msg = 'Izin lokasi ditolak oleh browser.';
                        else if (error.code === 2) msg = 'Posisi tidak dapat ditentukan.';
                        else if (error.code === 3) msg = 'Waktu pengambilan lokasi habis.';

                        window.dispatchEvent(new CustomEvent('notification', {
                            detail: {
                                status: 'danger',
                                title: 'Gagal Mengambil Lokasi',
                                body: msg
                            }
                        }));
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert('Browser Anda tidak mendukung Geolocation.');
            }
        });
    </script>
</x-filament-panels::page>
