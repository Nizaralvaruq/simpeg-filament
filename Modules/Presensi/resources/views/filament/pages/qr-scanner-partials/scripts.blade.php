<script>
    function qrScannerData() {
        return {
            mode: 'scan',
            scannedUser: null,
            errorMessage: '',
            currentTime: new Date(),
            isFullscreen: false,
            countdown: 10,
            showSuccessModal: false,
            offlineQueue: [],
            isSyncing: false, // Prevent concurrent syncs
            init() {
                setInterval(() => this.currentTime = new Date(), 1000);
                this.checkOnlineStatus();
                setInterval(() => this.checkOnlineStatus(), 5000);
                this.loadOfflineQueue();

                // Fullscreen change listener
                document.addEventListener('fullscreenchange', () => {
                    this.isFullscreen = !!document.fullscreenElement;
                });
            },
            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            },
            checkOnlineStatus() {
                @this.isOnline = navigator.onLine;
                if (navigator.onLine && this.offlineQueue.length > 0) {
                    this.syncOfflineQueue();
                }
            },
            loadOfflineQueue() {
                const queue = localStorage.getItem('qr_offline_queue');
                if (queue) {
                    this.offlineQueue = JSON.parse(queue);
                    @this.pendingScans = this.offlineQueue.length;
                }
            },
            saveOfflineQueue() {
                localStorage.setItem('qr_offline_queue', JSON.stringify(this.offlineQueue));
                @this.pendingScans = this.offlineQueue.length;
            },
            syncOfflineQueue() {
                if (this.isSyncing) return;

                // Sync offline scans when back online
                console.log('Syncing offline queue:', this.offlineQueue);
                this.isSyncing = true;
                const scansToSync = [...this.offlineQueue];

                // Call batch sync
                @this.syncOfflineScans(scansToSync).then(() => {
                    console.log('Sync complete');
                    // Remove successfully synced items (match by time & token to be safe)
                    this.offlineQueue = this.offlineQueue.filter(item =>
                        !scansToSync.some(synced => synced.time === item.time && synced.token === item
                            .token)
                    );
                    this.saveOfflineQueue();
                }).catch(err => {
                    console.error('Sync failed, keeping data in queue:', err);
                }).finally(() => {
                    this.isSyncing = false;
                });
            }
        }
    }
</script>

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrCode = new Html5Qrcode("reader");
            let isScanning = false;

            const startScanner = () => {
                html5QrCode.start({
                        facingMode: "environment"
                    }, {
                        fps: 24,
                        qrbox: {
                            width: 450,
                            height: 450
                        }
                    },
                    (decodedText) => {
                        if (isScanning) return;

                        // Offline Check
                        if (!navigator.onLine) {
                            handleOfflineScan(decodedText);
                            return;
                        }

                        isScanning = true;
                        document.getElementById('scan-overlay').style.opacity = '1';

                        setTimeout(() => {
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(
                                    (pos) => {
                                        document.getElementById('scan-overlay').style.opacity =
                                            '0';
                                        processScan(decodedText, pos.coords.latitude, pos.coords
                                            .longitude);
                                    },
                                    (err) => {
                                        document.getElementById('scan-overlay').style.opacity =
                                            '0';
                                        processScan(decodedText, null, null);
                                    }, {
                                        timeout: 3000,
                                        enableHighAccuracy: true
                                    }
                                );
                            } else {
                                document.getElementById('scan-overlay').style.opacity = '0';
                                processScan(decodedText, null, null);
                            }
                        }, 300);
                    }
                ).catch(err => {
                    console.error("Camera Error:", err);
                    // Auto restart if camera fails
                    setTimeout(startScanner, 2000);
                });
            };

            const handleOfflineScan = (token) => {
                const modal = document.querySelector('[x-data]').__x.$data;
                const scan = {
                    token: token,
                    time: new Date().toISOString(),
                    lat: null,
                    lng: null
                };
                modal.offlineQueue.push(scan);
                modal.saveOfflineQueue();

                // Fake success for offline user
                window.dispatchEvent(new CustomEvent('scan-success', {
                    detail: {
                        name: 'Offline Saved',
                        email: 'Sync waiting...',
                        avatar: null,
                        type: 'offline'
                    }
                }));
            };

            const processScan = (token, lat, lng) => {
                @this.processScan(token, lat, lng).then(() => {
                    // Quick lockout for fast scanning (1.5s)
                    setTimeout(() => isScanning = false, 1500);
                }).catch(() => {
                    isScanning = false;
                });
            };

            const manualSubmit = document.getElementById('manual-submit');
            if (manualSubmit) {
                manualSubmit.addEventListener('click', () => {
                    const token = document.getElementById('manual-token').value.trim();
                    if (token) {
                        window.dispatchEvent(new CustomEvent('close-modal', {
                            detail: {
                                id: 'manual-input-modal'
                            }
                        }));
                        document.getElementById('manual-token').value = '';
                        processScan(token, null, null);
                    }
                });
            }

            const manualTokenInput = document.getElementById('manual-token');
            if (manualTokenInput) {
                manualTokenInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') manualSubmit.click();
                });
            }

            // F11 for fullscreen
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F11') {
                    e.preventDefault();
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen();
                    } else {
                        document.exitFullscreen();
                    }
                }
            });

            // Prevent multiple starts on re-renders
            if (window.html5QrCode) {
                window.html5QrCode.stop().then(() => {
                    startScanner();
                }).catch(() => startScanner());
            } else {
                window.html5QrCode = html5QrCode;
                startScanner();
            }
        });

        window.addEventListener('scan-success', (event) => {
            const scannedUser = {
                name: event.detail.name,
                email: event.detail.email,
                avatar: event.detail.avatar,
                type: event.detail.type
            };

            Alpine.store('scanner', scannedUser);

            // Play appropriate sound
            const volume = @this.volume / 100;
            const audio = event.detail.type === 'check-in' ?
                document.getElementById('beep-checkin') :
                document.getElementById('beep-checkout');

            if (audio) {
                audio.volume = volume;
                audio.play();
            }

            // No visual effects (modal/confetti) as requested.
        });

        window.addEventListener('scan-error', (event) => {
            const volume = @this.volume / 100;
            const audio = document.getElementById('beep-error');
            if (audio) {
                audio.volume = volume;
                audio.play();
            }
        });
    </script>
@endpush
