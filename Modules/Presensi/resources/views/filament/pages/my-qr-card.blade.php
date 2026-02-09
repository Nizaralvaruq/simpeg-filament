<x-filament-panels::page>
    <style>
        .qr-card {
            background-color: white;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 1000px;
            /* Wider card */
            margin: 0 auto;
            border: 1px solid #e5e7eb;
            /* aspect-ratio: 1.58/1; Removed from global, moved to desktop */
        }

        @media (min-width: 768px) {
            .qr-card {
                flex-direction: row;
                aspect-ratio: 1.58/1;
                /* Apply fixed ratio only on desktop */
            }
        }

        .qr-card-left {
            background-color: #0066cc;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .qr-card-left {
                width: 35%;
                /* Adjust ratio for wider look */
            }
        }

        .qr-card-right {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }

        @media (min-width: 768px) {
            .qr-card-right {
                padding: 3rem;
            }
        }

        .qr-container {
            background-color: white;
            padding: 1.5rem;
            /* Reduced padding for mobile */
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 320px;
            /* Cap width for mobile readability */
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .qr-container {
                max-width: 100%;
                padding: 1rem;
                margin: 0;
            }
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.025em;
            background-color: #eff6ff;
            color: #0066cc;
            text-transform: uppercase;
            border: 1px solid #dbeafe;
            margin-bottom: 2rem;
        }

        .info-label {
            font-size: 0.625rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 0.25rem;
        }

        .info-value-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
            line-height: 1.25;
            margin-bottom: 1.5rem;
        }

        .info-value-jabatan {
            font-size: 1.125rem;
            font-weight: 700;
            color: #4b5563;
            line-height: 1.25;
            margin-bottom: 1.5rem;
        }

        .info-value-nip {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0066cc;
            font-family: monospace;
            letter-spacing: -0.025em;
        }

        .card-footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            font-size: 0.625rem;
            color: #9ca3af;
            font-style: italic;
        }

        .scan-text {
            color: white;
            font-weight: 500;
            letter-spacing: 0.025em;
            font-size: 0.875rem;
            opacity: 0.9;
            z-index: 10;
        }

        .bg-circle {
            position: absolute;
            width: 10rem;
            height: 10rem;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            filter: blur(40px);
        }
    </style>

    <div class="flex flex-col items-center justify-center min-h-[60vh] py-8">
        <!-- Main Card Container -->
        <div class="qr-card">

            <!-- Left Section: QR & Scan Area -->
            <div class="qr-card-left">
                <!-- Decorative Circle -->
                <div class="bg-circle" style="top: -2.5rem; left: -2.5rem;"></div>
                <div class="bg-circle" style="bottom: -2.5rem; right: -2.5rem; background-color: rgba(0,0,0,0.1);"></div>

                <!-- QR Container -->
                <div id="qrcode-container" class="qr-container">
                    <div id="qrcode" style="width: 100%; height: 100%;"></div>
                    <!-- Logo Overlay -->
                    <div
                        style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                        <div
                            style="background-color: white; padding: 0.25rem; border-radius: 9999px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 2px solid white;">
                            <img src="{{ asset('images/logo1.png') }}"
                                style="width: 2.5rem; height: 2.5rem; object-fit: contain;" alt="Logo">
                        </div>
                    </div>
                </div>

                <div class="scan-text">
                    Scan untuk Absen
                </div>
            </div>

            <!-- Right Section: Employee Info -->
            <div class="qr-card-right">
                <!-- Status Badge -->
                <div class="status-badge">
                    <span
                        style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #0066cc; margin-right: 0.5rem;"></span>
                    Aktif • Valid s/d {{ now()->addHours(1)->format('H:i') }}
                </div>

                <!-- Info Labels -->
                <div class="space-y-6">
                    <div>
                        <div class="info-label">
                            Nama Karyawan</div>
                        <div class="info-value-name">
                            {{ $this->getUserName() }}
                        </div>
                    </div>

                    <div>
                        <div class="info-label">
                            Amanah/Jabatan</div>
                        <div class="info-value-jabatan">
                            {{ $this->getUserJabatan() }}
                        </div>
                    </div>

                    <div>
                        <div class="info-label">
                            NPA / NIP</div>
                        <div class="info-value-nip">
                            {{ $this->getUserNip() }}
                        </div>
                    </div>
                </div>

                <!-- Footer / Secondary Info -->
                <div class="card-footer">
                    <div>Sistem Kepegawaian v2.0</div>
                    <div>QR Gen: {{ $this->getQrGeneratedDate() }}</div>
                </div>
            </div>
        </div>

        <!-- Instructions Section (Minimized) -->
        <div style="margin-top: 2rem; text-align: center; color: #9ca3af; font-size: 0.875rem; max-width: 32rem;">
            Tips: Gunakan tingkat kecerahan layar maksimal saat melakukan scanning pada mesin Terminal Absensi.
        </div>
    </div>

    <!-- Hidden canvas for PNG export -->
    <canvas id="qr-canvas" style="display:none;"></canvas>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        function initQrCode() {
            const container = document.getElementById("qrcode");

            // Clear previous QR code if exists
            container.innerHTML = '';

            // Dynamic size based on container width
            const containerWidth = container.clientWidth || 300; // Fallback

            const qrcode = new QRCode(container, {
                text: "{{ $this->getQrContent() }}",
                width: containerWidth,
                height: containerWidth,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H,
                useSVG: true
            });

            // Adjust the actual SVG inside to be responsive to the container
            setTimeout(() => {
                const svg = container.querySelector('svg');
                if (svg) {
                    svg.setAttribute('viewBox', `0 0 ${containerWidth} ${containerWidth}`);
                    svg.style.width = '100%';
                    svg.style.height = '100%';
                }
            }, 50);
        }

        // Initialize on page load (for non-SPA navigation)
        document.addEventListener('DOMContentLoaded', initQrCode);

        // Re-initialize on Livewire navigation (for SPA mode)
        document.addEventListener('livewire:navigated', initQrCode);

        // Download QR PNG handler
        window.addEventListener('download-qr-png', function() {
            const tempCanvas = document.createElement('canvas');
            const size = 1000;
            const logoSize = 160;

            new QRCode(tempCanvas, {
                text: "{{ $this->getQrContent() }}",
                width: size,
                height: size,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            setTimeout(() => {
                const qrCanvas = tempCanvas.querySelector('canvas');
                const ctx = qrCanvas.getContext('2d');
                const centerX = size / 2;
                const centerY = size / 2;
                const radius = (logoSize / 2) + 20;

                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                ctx.fillStyle = "white";
                ctx.fill();

                const logoImg = new Image();
                logoImg.src = "{{ asset('images/logo1.png') }}";
                logoImg.onload = function() {
                    const x = centerX - (logoSize / 2);
                    const y = centerY - (logoSize / 2);
                    ctx.drawImage(logoImg, x, y, logoSize, logoSize);

                    const link = document.createElement('a');
                    link.download = "QR-Branded-{{ $this->getUserNip() }}.png";
                    link.href = qrCanvas.toDataURL("image/png");
                    link.click();
                };
            }, 100);
        });
    </script>
</x-filament-panels::page>
