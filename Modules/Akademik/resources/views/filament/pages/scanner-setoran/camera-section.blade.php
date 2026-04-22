<div class="qr-card">
    {{-- CAMERA VIEWPORT --}}
    <div class="qr-camera" wire:ignore>
        <div id="reader" style="width:100%;height:100%;"></div>

        {{-- Live indicator --}}
        <div class="qr-live-badge">
            <span class="qr-live-dot"></span>
            Kamera Aktif
        </div>

        {{-- Scan frame corners --}}
        <div class="qr-frame">
            <div class="qr-frame-corner tl"></div>
            <div class="qr-frame-corner tr"></div>
            <div class="qr-frame-corner bl"></div>
            <div class="qr-frame-corner br"></div>
        </div>

        {{-- Scan animation line --}}
        <div class="qr-scan-line"></div>

        {{-- Loading overlay --}}
        <div id="scan-loading" class="qr-loading-overlay">
            <div class="qr-spinner"></div>
            <span class="text-cyan-300 font-black text-xs tracking-widest uppercase">Mencari Data Siswa…</span>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="qr-action-bar">
        <button type="button"
                @click="$dispatch('open-modal', { id: 'manual-input-modal' })"
                class="qr-btn qr-btn-outline flex-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            Input NIS Manual
        </button>

        <button type="button" id="btn-start-camera" class="qr-btn qr-btn-primary flex-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            Reset Kamera
        </button>
    </div>

</div>
