<x-filament-panels::page>
<style>
    .ss-wrap { max-width: 680px; margin: 0 auto; padding-bottom: 2rem; }

    /* Header Card */
    .ss-header-card {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
        border-radius: 1.25rem;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 8px 32px rgba(59,130,246,0.35);
        position: relative;
        overflow: hidden;
    }
    .ss-header-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 150px; height: 150px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .ss-header-card::after {
        content: '';
        position: absolute;
        bottom: -30px; left: 30%;
        width: 100px; height: 100px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .ss-header-icon {
        width: 3.5rem; height: 3.5rem;
        background: rgba(255,255,255,0.2);
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.25);
    }
    .ss-header-title { font-size: 1.25rem; font-weight: 800; letter-spacing: -0.01em; }
    .ss-header-sub { font-size: 0.8125rem; opacity: 0.85; margin-top: 0.1rem; }

    /* Camera Card */
    .ss-camera-card {
        background: white;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        margin-bottom: 1.25rem;
        border: 1px solid #e5e7eb;
    }
    .dark .ss-camera-card { background: #1f2937; border-color: #374151; }

    .ss-camera-viewport {
        position: relative;
        background: #0f172a;
        aspect-ratio: 4/3;
        overflow: hidden;
    }
    @media(min-width:640px){
        .ss-camera-viewport { aspect-ratio: 16/9; }
    }
    #ss-reader { width:100%; height:100%; }
    #ss-reader video { object-fit:cover!important; width:100%!important; height:100%!important; }
    #ss-reader, #ss-reader__scan_region, #ss-reader__dashboard_section, #ss-reader__header_message {
        border: none!important;
    }
    #ss-reader__scan_region, #ss-reader__dashboard_section, #ss-reader__header_message { display:none!important; }

    /* Scanning line animation */
    .ss-scan-line {
        position: absolute; top:0; left:0; width:100%; height:3px;
        background: linear-gradient(to right, transparent, #38bdf8, #3b82f6, #38bdf8, transparent);
        animation: ssScan 2.5s ease-in-out infinite;
        box-shadow: 0 0 12px #38bdf8;
        pointer-events: none;
    }
    @keyframes ssScan {
        0%   { top:5%; opacity:0; }
        10%  { opacity:1; }
        90%  { opacity:1; }
        100% { top:95%; opacity:0; }
    }

    /* Corner brackets overlay */
    .ss-corners {
        position: absolute; inset:0;
        pointer-events: none;
        display: flex; align-items: center; justify-content: center;
    }
    .ss-corners-inner {
        width: min(60%, 240px);
        aspect-ratio: 1;
        position: relative;
    }
    .ss-corners-inner::before,
    .ss-corners-inner::after,
    .ss-corner-br::before,
    .ss-corner-br::after {
        content: '';
        position: absolute;
        width: 28px; height: 28px;
        border-color: rgba(56,189,248,0.9);
        border-style: solid;
    }
    .ss-corners-inner::before { top:0; left:0; border-width:3px 0 0 3px; border-radius:4px 0 0 0; }
    .ss-corners-inner::after  { top:0; right:0; border-width:3px 3px 0 0; border-radius:0 4px 0 0; }
    .ss-corner-br::before { bottom:0; left:0; border-width:0 0 3px 3px; border-radius:0 0 0 4px; }
    .ss-corner-br::after  { bottom:0; right:0; border-width:0 3px 3px 0; border-radius:0 0 4px 0; }

    .ss-live-badge {
        position: absolute; top:1rem; left:1rem;
        display: inline-flex; align-items: center; gap:0.4rem;
        background: rgba(15,23,42,0.75);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 9999px;
        padding: 0.3rem 0.75rem;
        font-size: 0.7rem; font-weight: 600; color: white;
    }
    .ss-live-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #ef4444;
        animation: ssPulse 1.4s ease-in-out infinite;
    }
    @keyframes ssPulse { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

    .ss-camera-actions {
        padding: 0.875rem 1rem;
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        display: flex; gap: 0.625rem; flex-wrap: wrap;
    }
    .dark .ss-camera-actions { background: rgba(31,41,55,0.6); border-top-color: #374151; }

    .ss-btn {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.5rem 1rem; border-radius: 0.625rem;
        font-size: 0.8125rem; font-weight: 600;
        cursor: pointer; border: none;
        transition: all 0.18s;
    }
    .ss-btn-primary { background: #2563eb; color: white; }
    .ss-btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); }
    .ss-btn-secondary { background: #e2e8f0; color: #475569; }
    .dark .ss-btn-secondary { background: #374151; color: #d1d5db; }
    .ss-btn-secondary:hover { background: #cbd5e1; }

    /* Result Overlay */
    .ss-result-overlay {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        z-index: 50;
        backdrop-filter: blur(8px);
    }
    .ss-result-success { background: rgba(5,150,105,0.88); }
    .ss-result-error   { background: rgba(220,38,38,0.88); }

    .ss-result-box {
        text-align: center; color: white;
        padding: 2rem 1.5rem;
        max-width: 320px;
    }
    .ss-result-icon {
        width: 64px; height: 64px; border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
        border: 2px solid rgba(255,255,255,0.4);
    }
    .ss-result-title { font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem; }
    .ss-result-msg   { font-size: 0.875rem; opacity: 0.9; line-height: 1.5; }
    .ss-result-detail {
        margin-top: 1rem;
        background: rgba(255,255,255,0.15);
        border-radius: 0.75rem;
        padding: 0.75rem;
        font-size: 0.8125rem;
        text-align: left;
    }
    .ss-result-detail div { margin-bottom: 0.3rem; }

    /* History Card */
    .ss-history-card {
        background: white;
        border-radius: 1.25rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .ss-history-card { background: #1f2937; border-color: #374151; }

    .ss-history-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .dark .ss-history-header { border-bottom-color: #374151; }
    .ss-history-title {
        font-size: 0.9375rem; font-weight: 700; color: #111827;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .dark .ss-history-title { color: #f1f5f9; }

    .ss-history-body { padding: 0.75rem 1rem; }

    .ss-history-item {
        display: flex; align-items: center; gap: 0.875rem;
        padding: 0.75rem;
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        animation: ssSlideIn 0.3s ease-out;
    }
    .dark .ss-history-item { background: rgba(255,255,255,0.04); border-color: #374151; }
    @keyframes ssSlideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }

    .ss-history-icon {
        width: 2.5rem; height: 2.5rem; border-radius: 0.625rem;
        background: #dcfce7; color: #16a34a;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .ss-history-kegiatan { font-size: 0.875rem; font-weight: 600; color: #111827; }
    .dark .ss-history-kegiatan { color: #e2e8f0; }
    .ss-history-meta { font-size: 0.75rem; color: #6b7280; display: flex; gap: 0.75rem; margin-top: 0.15rem; flex-wrap: wrap; }

    .ss-badge {
        display: inline-flex; align-items: center;
        padding: 0.1rem 0.5rem; border-radius: 9999px;
        font-size: 0.7rem; font-weight: 700;
    }
    .ss-badge-self { background: #ede9fe; color: #7c3aed; }
    .ss-badge-hadir { background: #dcfce7; color: #166534; }

    .ss-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #94a3b8;
    }
    .ss-empty svg { margin: 0 auto 0.75rem; }

    [x-cloak] { display: none !important; }
</style>

<div
    x-data="selfScanData()"
    x-init="init()"
    class="ss-wrap"
>
    {{-- Header --}}
    <div class="ss-header-card">
        <div class="ss-header-icon">
            <svg style="width:1.75rem;height:1.75rem;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div style="position:relative;z-index:1;">
            <div class="ss-header-title">Scan Kehadiran</div>
            <div class="ss-header-sub">Pindai QR Code kegiatan untuk mencatat kehadiran Anda secara mandiri</div>
        </div>
    </div>

    {{-- Camera Card --}}
    <div class="ss-camera-card">
        <div class="ss-camera-viewport">
            <div id="ss-reader" wire:ignore></div>

            {{-- Corner brackets --}}
            <div class="ss-corners">
                <div class="ss-corners-inner">
                    <div class="ss-corner-br"></div>
                </div>
            </div>

            {{-- Scan line --}}
            <div class="ss-scan-line" x-show="scanning"></div>

            {{-- Live badge --}}
            <div class="ss-live-badge">
                <div class="ss-live-dot"></div>
                Kamera Aktif
            </div>

            {{-- Success Overlay --}}
            <div class="ss-result-overlay ss-result-success"
                x-show="showResult && resultType === 'success'"
                x-cloak
                @click="closeResult()"
            >
                <div class="ss-result-box">
                    <div class="ss-result-icon">
                        <svg style="width:2rem;height:2rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ss-result-title">Absensi Berhasil!</div>
                    <div class="ss-result-msg" x-text="resultMessage"></div>
                    <div class="ss-result-detail" x-show="resultData">
                        <div><strong>📋</strong> <span x-text="resultData?.kegiatan"></span></div>
                        <div><strong>🕐</strong> <span x-text="resultData?.waktu"></span></div>
                        <div><strong>📍</strong> <span x-text="resultData?.lokasi"></span></div>
                    </div>
                    <div style="margin-top:1rem;font-size:0.75rem;opacity:0.75;">Ketuk untuk menutup • Scan berikutnya siap</div>
                </div>
            </div>

            {{-- Error Overlay --}}
            <div class="ss-result-overlay ss-result-error"
                x-show="showResult && resultType === 'error'"
                x-cloak
                @click="closeResult()"
            >
                <div class="ss-result-box">
                    <div class="ss-result-icon">
                        <svg style="width:2rem;height:2rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="ss-result-title">Gagal!</div>
                    <div class="ss-result-msg" x-text="resultMessage"></div>
                    <div style="margin-top:1rem;font-size:0.75rem;opacity:0.75;">Ketuk untuk mencoba lagi</div>
                </div>
            </div>
        </div>

        {{-- Camera Actions --}}
        <div class="ss-camera-actions">
            <button type="button" class="ss-btn ss-btn-primary" id="ss-btn-start">
                <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Aktifkan Kamera
            </button>
            <button type="button" class="ss-btn ss-btn-secondary" @click="$dispatch('open-modal', { id: 'ss-manual-modal' })">
                <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Input Manual
            </button>
        </div>
    </div>

    {{-- Riwayat Hari Ini --}}
    <div class="ss-history-card">
        <div class="ss-history-header">
            <div class="ss-history-title">
                <svg style="width:1.125rem;height:1.125rem;color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Riwayat Hari Ini
            </div>
            <span style="font-size:0.75rem;color:#6b7280;">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
        <div class="ss-history-body">
            @forelse ($this->riwayatHariIni as $item)
                <div class="ss-history-item">
                    <div class="ss-history-icon">
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="ss-history-kegiatan">{{ $item['kegiatan_nama'] }}</div>
                        <div class="ss-history-meta">
                            <span>🕐 {{ $item['jam_absen'] }}</span>
                            <span class="ss-badge ss-badge-hadir">{{ ucfirst($item['status']) }}</span>
                            <span class="ss-badge ss-badge-self">Self Scan</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="ss-empty">
                    <svg style="width:3rem;height:3rem;color:#cbd5e1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p style="font-size:0.875rem;">Belum ada absensi kegiatan hari ini.</p>
                    <p style="font-size:0.75rem;margin-top:0.25rem;">Scan QR Code kegiatan untuk mencatat kehadiran.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Manual Input Modal --}}
<x-filament::modal id="ss-manual-modal" width="sm">
    <x-slot name="heading">Input Token Manual</x-slot>
    <x-slot name="description">Masukkan token kegiatan secara manual jika kamera tidak tersedia.</x-slot>
    <div style="padding: 0.5rem 0;">
        <input
            id="ss-manual-input"
            type="text"
            placeholder="Contoh: KEGIATAN-5"
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid #d1d5db;border-radius:0.625rem;font-size:0.9rem;outline:none;transition:border-color 0.15s;"
            onkeypress="if(event.key==='Enter') document.getElementById('ss-manual-submit').click()"
        >
    </div>
    <x-slot name="footerActions">
        <button id="ss-manual-submit" type="button" onclick="selfScanManual()" class="ss-btn ss-btn-primary" style="width:100%;justify-content:center;">
            Proses Absensi
        </button>
    </x-slot>
</x-filament::modal>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    window.ssComponentId = '{{ $this->getId() }}';

    function selfScanData() {
        return {
            scanning: true,
            showResult: false,
            resultType: 'success', // 'success' | 'error'
            resultMessage: '',
            resultData: null,
            isProcessing: false,
            init() {
                this.$nextTick(() => this.startCamera());

                // Listen for Livewire events
                window.addEventListener('self-scan-success', (e) => {
                    this.isProcessing = false;
                    this.resultType    = 'success';
                    this.resultMessage = @this.message;
                    this.resultData    = e.detail.result ?? null;
                    this.showResult    = true;
                    this.playSound('success');
                    setTimeout(() => this.closeResult(), 4000);
                });

                window.addEventListener('self-scan-error', (e) => {
                    this.isProcessing  = false;
                    this.resultType    = 'error';
                    this.resultMessage = e.detail.message ?? 'Terjadi kesalahan.';
                    this.resultData    = null;
                    this.showResult    = true;
                    this.playSound('error');
                    setTimeout(() => this.closeResult(), 3500);
                });
            },
            startCamera() {
                const initFn = () => {
                    if (typeof Html5Qrcode === 'undefined') {
                        setTimeout(initFn, 400);
                        return;
                    }
                    const el = document.getElementById('ss-reader');
                    if (!el) return;
                    if (window._ssQrInstance) {
                        try { window._ssQrInstance.stop().catch(()=>{}); } catch(e){}
                    }
                    window._ssQrInstance = new Html5Qrcode('ss-reader');
                    window._ssQrInstance.start(
                        { facingMode: 'environment' },
                        {
                            fps: 20,
                            qrbox: (w, h) => {
                                const s = Math.floor(Math.min(w, h) * 0.72);
                                return { width: s, height: s };
                            }
                        },
                        (decodedText) => {
                            if (this.isProcessing || this.showResult) return;
                            this.isProcessing = true;
                            this.triggerScan(decodedText);
                        }
                    ).catch(err => console.error('QR start error:', err));
                };
                initFn();
            },
            triggerScan(token) {
                const component = Livewire.find(window.ssComponentId);
                if (!component) { this.isProcessing = false; return; }
                component.call('processScan', token).catch(() => { this.isProcessing = false; });
            },
            closeResult() {
                this.showResult = false;
                this.isProcessing = false;
            },
            playSound(type) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    if (type === 'success') {
                        osc.frequency.setValueAtTime(880, ctx.currentTime);
                        osc.frequency.setValueAtTime(1100, ctx.currentTime + 0.1);
                    } else {
                        osc.frequency.setValueAtTime(300, ctx.currentTime);
                    }
                    gain.gain.setValueAtTime(0.3, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.3);
                } catch(e) {}
            }
        }
    }

    function selfScanManual() {
        const input = document.getElementById('ss-manual-input');
        const token = input ? input.value.trim() : '';
        if (!token) return;
        input.value = '';
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'ss-manual-modal' } }));
        const component = Livewire.find(window.ssComponentId);
        if (component) component.call('processScan', token);
    }

    // Start camera button
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('ss-btn-start');
        if (btn) btn.addEventListener('click', () => {
            const data = document.querySelector('[x-data]').__x?.$data;
            if (data && data.startCamera) data.startCamera();
        });
    });
</script>
@endpush
</x-filament-panels::page>
