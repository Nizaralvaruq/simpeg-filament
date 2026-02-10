<x-filament-panels::page>
    <form>
        {{ $this->form }}
    </form>

    <script>
        // Store the component ID for this page load
        window.filamentSettingsComponentId = '{{ $this->getId() }}';

        if (!window.hasRegisteredLocationHandler) {
            window.addEventListener('get-current-location', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            // 1. Force update DOM elements immediately for visual feedback
                            const latInput = document.getElementById('office-latitude');
                            const lngInput = document.getElementById('office-longitude');

                            if (latInput) {
                                latInput.value = lat;
                                latInput.dispatchEvent(new Event('input', {
                                    bubbles: true
                                }));
                            }
                            if (lngInput) {
                                lngInput.value = lng;
                                lngInput.dispatchEvent(new Event('input', {
                                    bubbles: true
                                }));
                            }

                            // 2. Update Livewire state using the CURRENT component ID
                            const component = Livewire.find(window.filamentSettingsComponentId);
                            if (component) {
                                component.set('data.office_latitude', lat);
                                component.set('data.office_longitude', lng);
                            }

                            // 3. Dispatch Notification
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
                            let title = 'Gagal Mengambil Lokasi';

                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    title = 'Izin Ditolak';
                                    msg =
                                        'Mohon izinkan akses lokasi di browser Anda. Klik icon gembok di address bar -> Reset Permission / Allow Location.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    title = 'Posisi Tidak Tersedia';
                                    msg = 'Browser tidak dapat menemukan lokasi Anda. Pastikan GPS aktif.';
                                    break;
                                case error.TIMEOUT:
                                    title = 'Waktu Habis';
                                    msg = 'Terlalu lama mengambil lokasi. Coba refresh halaman.';
                                    break;
                                default:
                                    msg = 'Error tidak diketahui: ' + error.message;
                            }

                            window.dispatchEvent(new CustomEvent('notification', {
                                detail: {
                                    status: 'danger',
                                    title: title,
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

            window.hasRegisteredLocationHandler = true;
        }
    </script>
</x-filament-panels::page>
