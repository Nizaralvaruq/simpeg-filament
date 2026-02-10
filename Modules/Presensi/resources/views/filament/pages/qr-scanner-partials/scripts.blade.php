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
        // Store component ID globally for dynamic access
        window.filamentQrScannerComponentId = '{{ $this->getId() }}';

        if (!window.hasInitializedQrScanner) {
            window.hasInitializedQrScanner = true;

            // Helper to get audio source safely
            const getAudioSrc = (id) => {
                const el = document.getElementById(id);
                if (!el) return null;
                return el.currentSrc || el.querySelector('source')?.src;
            };

            // Singleton Audio Elements
            let audioCheckIn, audioCheckOut, audioError;

            let html5QrCode = null;
            let isScanning = false;

            // Master init function
            window.initQrScannerMaster = function() {
                // Init Audio if not ready
                if (!audioCheckIn && document.getElementById('beep-checkin')) {
                    audioCheckIn = new Audio(getAudioSrc('beep-checkin'));
                    audioCheckOut = new Audio(getAudioSrc('beep-checkout'));
                    audioError = new Audio(getAudioSrc('beep-error'));
                }

                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        startScanner();
                    }).catch(() => startScanner());
                } else {
                    html5QrCode = new Html5Qrcode("reader");
                    startScanner();
                }

                bindManualInput();
            };

            const bindManualInput = () => {
                const manualSubmit = document.getElementById('manual-submit');
                const manualTokenInput = document.getElementById('manual-token');

                if (manualSubmit) {
                    manualSubmit.onclick = () => {
                        const token = manualTokenInput.value.trim();
                        if (token) {
                            window.dispatchEvent(new CustomEvent('close-modal', {
                                detail: {
                                    id: 'manual-input-modal'
                                }
                            }));
                            manualTokenInput.value = '';
                            processScan(token, null, null);
                        }
                    };
                }

                if (manualTokenInput) {
                    manualTokenInput.onkeypress = (e) => {
                        if (e.key === 'Enter') manualSubmit.click();
                    };
                }
            };

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
                        if (document.getElementById('scan-overlay')) document.getElementById('scan-overlay').style
                            .opacity = '1';

                        setTimeout(() => {
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(
                                    (pos) => {
                                        if (document.getElementById('scan-overlay')) document
                                            .getElementById('scan-overlay').style.opacity = '0';
                                        processScan(decodedText, pos.coords.latitude, pos.coords
                                            .longitude);
                                    },
                                    (err) => {
                                        // Debug Alert
                                        alert(`GPS Error (${err.code}): ${err.message}`);
                                        if (document.getElementById('scan-overlay')) document
                                            .getElementById('scan-overlay').style.opacity = '0';
                                        processScan(decodedText, null, null);
                                    }, {
                                        timeout: 10000,
                                        enableHighAccuracy: true
                                    }
                                );
                            } else {
                                if (document.getElementById('scan-overlay')) document.getElementById(
                                    'scan-overlay').style.opacity = '0';
                                processScan(decodedText, null, null);
                            }
                        }, 300);
                    }
                ).catch(err => {
                    console.error("Camera Error:", err);
                    setTimeout(startScanner, 2000);
                });
            };

            const processScan = (token, lat, lng) => {
                // Dynamic Component Lookup
                const component = Livewire.find(window.filamentQrScannerComponentId);

                if (component) {
                    component.call('processScan', token, lat, lng).then(() => {
                        setTimeout(() => isScanning = false, 1500);
                    }).catch(() => {
                        isScanning = false;
                    });
                } else {
                    console.error("Scanner Component Not Found");
                    isScanning = false;
                }
            };

            const handleOfflineScan = (token) => {
                const modal = document.querySelector('[x-data]').__x.$data;
                const scan = {
                    token: token,
                    time: new Date().toISOString(),
                    lat: null,
                    lng: null
                };
                if (modal && modal.offlineQueue) {
                    modal.offlineQueue.push(scan);
                    modal.saveOfflineQueue();

                    window.dispatchEvent(new CustomEvent('scan-success', {
                        detail: {
                            name: 'Offline Saved',
                            email: 'Sync waiting...',
                            avatar: null,
                            type: 'offline'
                        }
                    }));
                }
            };

            // Events - Added ONCE
            window.addEventListener('scan-success', (event) => {
                const component = Livewire.find(window.filamentQrScannerComponentId);
                const volume = (component?.volume ?? 70) / 100;

                // 1. Play Sound
                if (event.detail.type === 'check-in') {
                    if (audioCheckIn) {
                        audioCheckIn.volume = volume;
                        audioCheckIn.play();
                    }
                } else if (event.detail.type === 'check-out') {
                    if (audioCheckOut) {
                        audioCheckOut.volume = volume;
                        audioCheckOut.play();
                    }
                }

                // 2. Update Alpine Store (legacy support)
                Alpine.store('scanner', event.detail);

                // 3. Trigger Modal (Improvement)
                const alpineEl = document.querySelector('[x-data]');
                if (alpineEl && alpineEl.__x) {
                    const data = alpineEl.__x.$data;
                    data.scannedUser = event.detail;
                    data.showSuccessModal = true;
                    data.countdown = 10;

                    // Countdown Logic
                    if (window.scanCountdownInterval) clearInterval(window.scanCountdownInterval);
                    window.scanCountdownInterval = setInterval(() => {
                        if (data.countdown > 0) {
                            data.countdown--;
                        } else {
                            data.showSuccessModal = false;
                            clearInterval(window.scanCountdownInterval);
                        }
                    }, 1000);
                }
            });

            window.addEventListener('scan-error', (event) => {
                const component = Livewire.find(window.filamentQrScannerComponentId);
                const volume = (component?.volume ?? 70) / 100;
                if (audioError) {
                    audioError.volume = volume;
                    audioError.play();
                }
            });

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

            document.addEventListener('livewire:navigated', () => {
                if (document.getElementById('reader')) {
                    window.initQrScannerMaster();
                } else {
                    if (html5QrCode) {
                        html5QrCode.stop().catch(e => {});
                    }
                }
            });
        }

        if (document.getElementById('reader')) {
            if (window.initQrScannerMaster) {
                window.initQrScannerMaster();
            } else {
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.initQrScannerMaster) window.initQrScannerMaster();
                });
            }
        }
    </script>
@endpush
