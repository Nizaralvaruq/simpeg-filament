<style>
    .qr-modal-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        padding: 1rem 0 0.5rem;
    }

    .qr-kegiatan-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 9999px;
        padding: 0.375rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #1d4ed8;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .qr-modal-image-wrap {
        background: white;
        border-radius: 1.5rem;
        padding: 1.25rem;
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0,0,0,0.04);
        position: relative;
    }

    .qr-modal-image-wrap img {
        width: 240px;
        height: 240px;
        display: block;
    }

    .qr-modal-corners {
        position: absolute;
        inset: -4px;
        pointer-events: none;
    }

    .qr-modal-corners::before,
    .qr-modal-corners::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #3b82f6;
        border-style: solid;
    }

    .qr-modal-corners::before {
        top: 8px;
        left: 8px;
        border-width: 3px 0 0 3px;
        border-radius: 4px 0 0 0;
    }

    .qr-modal-corners::after {
        bottom: 8px;
        right: 8px;
        border-width: 0 3px 3px 0;
        border-radius: 0 0 4px 0;
    }

    .qr-kegiatan-info {
        text-align: center;
        width: 100%;
    }

    .qr-kegiatan-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .qr-kegiatan-meta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        font-size: 0.8125rem;
        color: #6b7280;
        flex-wrap: wrap;
    }

    .qr-kegiatan-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .qr-token-display {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 0.75rem;
        padding: 0.625rem 1.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #475569;
        letter-spacing: 0.05em;
        text-align: center;
    }

    .qr-modal-tip {
        background: #fef9c3;
        border: 1px solid #fde047;
        border-radius: 0.75rem;
        padding: 0.625rem 1rem;
        font-size: 0.8rem;
        color: #713f12;
        text-align: center;
        max-width: 300px;
    }

    .qr-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }

    .qr-status-open {
        background: #dcfce7;
        color: #15803d;
    }

    .qr-status-closed {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* --- Download / Action Buttons --- */
    .qr-action-group {
        display: flex;
        gap: 0.625rem;
        flex-wrap: wrap;
        justify-content: center;
        width: 100%;
    }

    .qr-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5625rem 1.25rem;
        border-radius: 0.625rem;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all 0.18s ease;
        line-height: 1;
    }

    .qr-action-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .qr-action-btn:active { transform: translateY(0); }

    .qr-btn-download {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(37,99,235,0.35);
    }

    .qr-btn-print {
        background: #f1f5f9;
        color: #475569;
        border: 1.5px solid #e2e8f0;
    }

    .qr-btn-print:hover { background: #e2e8f0; }

    /* Print-only: hide everything except QR */
    @media print {
        body > * { display: none !important; }
        .qr-print-area { display: block !important; }
    }
</style>

<div class="qr-modal-wrapper">
    {{-- Status Badge --}}
    <div class="qr-kegiatan-badge">
        <svg style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.243m-4.243 0H7.757M12 12V7.757m0 4.243V16.5"/>
        </svg>
        QR Scan Kehadiran
    </div>

    {{-- QR Code Image --}}
    <div class="qr-modal-image-wrap">
        <div class="qr-modal-corners"></div>
        <img src="{{ $qrBase64 }}" alt="QR Code {{ $kegiatan->nama_kegiatan }}">
    </div>

    {{-- Kegiatan Info --}}
    <div class="qr-kegiatan-info">
        <div class="qr-kegiatan-name">{{ $kegiatan->nama_kegiatan }}</div>
        <div class="qr-kegiatan-meta">
            <span>
                <svg style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') }}
            </span>
            <span>
                <svg style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ substr($kegiatan->jam_mulai, 0, 5) }} – {{ substr($kegiatan->jam_selesai, 0, 5) }}
            </span>
            <span>
                <svg style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $kegiatan->lokasi }}
            </span>
        </div>

        <div style="margin-top:0.75rem; display:flex; justify-content:center; gap:0.5rem; flex-wrap:wrap;">
            <span class="qr-status-pill {{ $kegiatan->is_closed ? 'qr-status-closed' : 'qr-status-open' }}">
                <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                {{ $kegiatan->is_closed ? 'Absensi Ditutup' : 'Absensi Terbuka' }}
            </span>
            @if($kegiatan->is_wajib)
                <span class="qr-status-pill" style="background:#fef3c7;color:#92400e;">
                    ⚠ Wajib Hadir
                </span>
            @endif
        </div>
    </div>

    {{-- Token Display --}}
    <div class="qr-token-display">{{ $token }}</div>

    {{-- Action Buttons --}}
    <div class="qr-action-group">
        {{-- Download PNG via server route --}}
        <a
            href="{{ $downloadUrl }}"
            download
            class="qr-action-btn qr-btn-download"
            id="qr-download-btn"
        >
            <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download PNG
        </a>

        {{-- Save directly from base64 (client-side, no server round-trip) --}}
        <button
            type="button"
            class="qr-action-btn qr-btn-download"
            style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: 0 2px 8px rgba(5,150,105,0.35);"
            onclick="saveQrPng()"
        >
            <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            Simpan Cepat
        </button>

        {{-- Print --}}
        <button
            type="button"
            class="qr-action-btn qr-btn-print"
            onclick="printQr()"
        >
            <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak
        </button>
    </div>

    {{-- Tip --}}
    <div class="qr-modal-tip">
        💡 Proyeksikan QR ini di layar / cetak dan tempel di lokasi kegiatan agar pegawai dapat scan mandiri.
    </div>
</div>

{{-- Hidden print area --}}
<div id="qr-print-area" style="display:none;">
    <div style="text-align:center; font-family: sans-serif; padding: 2rem;">
        <img
            id="qr-print-img"
            src="{{ $qrBase64 }}"
            style="width:280px; height:280px; display:block; margin: 0 auto 1rem;"
            alt="QR Code Kegiatan"
        >
        <h2 style="font-size:1.25rem; font-weight:700; margin:0.5rem 0;">{{ $kegiatan->nama_kegiatan }}</h2>
        <p style="color:#6b7280; margin:0.25rem 0; font-size:0.9rem;">{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') }}</p>
        <p style="color:#6b7280; margin:0.25rem 0; font-size:0.9rem;">{{ substr($kegiatan->jam_mulai, 0, 5) }} – {{ substr($kegiatan->jam_selesai, 0, 5) }} | {{ $kegiatan->lokasi }}</p>
        <p style="font-size:0.8rem; color:#94a3b8; margin-top:1rem; font-family:monospace;">{{ $token }}</p>
        <p style="font-size:0.75rem; color:#cbd5e1; margin-top:0.5rem;">Scan QR ini untuk absensi mandiri</p>
    </div>
</div>

<script>
    // Save via base64 (instant, no server trip)
    function saveQrPng() {
        const src  = document.getElementById('qr-print-img')?.src;
        if (!src) return;
        const a    = document.createElement('a');
        a.href     = src;
        a.download = 'QR-{{ str($kegiatan->nama_kegiatan)->slug() }}-{{ $kegiatan->id }}.png';
        a.click();
    }

    // Print
    function printQr() {
        const printArea = document.getElementById('qr-print-area');
        if (!printArea) return;
        const w = window.open('', '_blank', 'width=500,height=600');
        w.document.write('<html><head><title>QR Kegiatan</title>');
        w.document.write('<style>body{margin:0;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#fff;} img{max-width:100%;}</style>');
        w.document.write('</head><body>');
        w.document.write(printArea.innerHTML);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        setTimeout(() => { w.print(); }, 400);
    }
</script>
