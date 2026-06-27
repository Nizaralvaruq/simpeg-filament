<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    function qrScannerData() {
        return {
            currentTime:  new Date(),
            isFullscreen: false,
            isScanning:   false,
            html5QrCode:  null,
            audioSuccess: null,
            audioError:   null,
            audioSaved:   null,

            init() {
                // Clock
                setInterval(() => this.currentTime = new Date(), 1000);

                // Load audio refs after DOM is ready
                this.$nextTick(() => {
                    this.audioSuccess = document.getElementById('beep-success');
                    this.audioError   = document.getElementById('beep-error');
                    this.audioSaved   = document.getElementById('beep-saved');
                    this.startScannerWithRetry();

                    // "Reset Camera" button (ada di DOM utama, aman di sini)
                    const btnReset = document.getElementById('btn-start-camera');
                    if (btnReset) {
                        btnReset.onclick = () => {
                            this.isScanning = false;
                            this.startScannerWithRetry();
                        };
                    }
                });

                // Fullscreen listener
                document.addEventListener('fullscreenchange', () => {
                    this.isFullscreen = !!document.fullscreenElement;
                });

                // Manual NIS dari modal (pakai window event karena Filament modal di-teleport ke <body>)
                window.addEventListener('manual-nis-submit', (e) => {
                    const nis = e.detail?.nis;
                    if (nis) this.onScanned(nis);
                });

                // Livewire events
                window.addEventListener('scan-success', () => this.playAudio(this.audioSuccess));
                window.addEventListener('scan-error',   () => {
                    this.playAudio(this.audioError);
                    // Buka kembali scanner jika scan gagal (termasuk saat guru klik Batal di modal konfirmasi)
                    this.isScanning = false;
                });
                window.addEventListener('setoran-saved',() => {
                    this.playAudio(this.audioSaved);
                    // Scroll history list to top
                    const list = document.getElementById('history-list');
                    if (list) list.scrollTop = 0;
                    // Restart scanner for next student
                    setTimeout(() => {
                        this.isScanning = false;
                        this.startScannerWithRetry();
                    }, 800);
                });
            },

            // ── Scanner ─────────────────────────────────────────────
            startScannerWithRetry(attempt = 0) {
                if (typeof Html5Qrcode === 'undefined') {
                    if (attempt < 20) setTimeout(() => this.startScannerWithRetry(attempt + 1), 400);
                    else console.error('html5-qrcode failed to load.');
                    return;
                }
                const reader = document.getElementById('reader');
                if (!reader) return;

                // Stop existing instance cleanly
                const doStart = () => {
                    if (!this.html5QrCode) {
                        this.html5QrCode = new Html5Qrcode('reader');
                    }
                    this.launchCamera();
                };

                if (this.html5QrCode) {
                    try {
                        const state = this.html5QrCode.getState();
                        if (state === 1) { // 1 = Html5QrcodeScannerState.RUNNING
                            this.html5QrCode.stop()
                                .then(() => { this.html5QrCode.clear(); doStart(); })
                                .catch(() => doStart());
                            return;
                        }
                    } catch(e) { this.html5QrCode = null; }
                }
                doStart();
            },

            launchCamera() {
                if (!document.getElementById('reader')) return;

                const config = {
                    fps: 20,
                    qrbox: (w, h) => {
                        const size = Math.floor(Math.min(w, h) * 0.72);
                        return { width: size, height: size };
                    },
                    aspectRatio: 16/9,
                };

                this.html5QrCode.start(
                    { facingMode: 'environment' },
                    config,
                    (decodedText) => {
                        if (this.isScanning) return;
                        this.onScanned(decodedText);
                    }
                ).catch(err => console.error('Camera error:', err));
            },

            onScanned(token) {
                this.isScanning = true;
                const overlay = document.getElementById('scan-loading');
                if (overlay) overlay.style.opacity = '1';

                this.$wire.processScan(token).then((result) => {
                    if (overlay) overlay.style.opacity = '0';

                    if (this.$wire.showConfirmNewSiswa) {
                        this.isScanning = false;
                    }
                }).catch((err) => {
                    if (overlay) overlay.style.opacity = '0';
                    this.isScanning = false;
                    console.error('Scan process error:', err);
                });
            },

            // ── Fullscreen ──────────────────────────────────────────
            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            },

            // ── Audio ───────────────────────────────────────────────
            playAudio(audio) {
                if (!audio) return;
                try {
                    audio.volume = Math.min(1, Math.max(0, (this.$wire.volume ?? 70) / 100));
                    audio.currentTime = 0;
                    audio.play().catch(() => {});
                } catch(e) {}
            },
        };
    }
</script>
