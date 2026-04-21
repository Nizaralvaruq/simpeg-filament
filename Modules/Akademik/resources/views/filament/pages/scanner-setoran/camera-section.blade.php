<div class="qr-card">
    {{-- CAMERA VIEWPORT --}}
    <div class="qr-camera" wire:ignore>
        <div id="reader" style="width:100%;height:100%;"></div>

        {{-- Live indicator --}}
        <div class="qr-badge">
            <span class="qr-pulse">
                <span class="qr-pulse-ring"></span>
                <span class="qr-pulse-dot"></span>
            </span>
            KAMERA AKTIF
        </div>

        {{-- Scan animation line --}}
        <div class="qr-scan-line"></div>

        {{-- Loading / processing overlay --}}
        <div id="scan-loading"
             class="absolute inset-0 flex flex-col items-center justify-center gap-4 transition-opacity duration-300"
             style="background:rgba(15,23,42,.75);opacity:0;pointer-events:none;z-index:20;">
            <div class="w-14 h-14 border-4 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-white font-black text-sm tracking-widest">MENCARI DATA SISWA…</span>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="qr-actions">
        <button type="button"
                @click="$dispatch('open-modal', { id: 'manual-input-modal' })"
                class="qr-btn qr-btn-outline grow justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            Input NIS Manual
        </button>

        <button type="button" id="btn-start-camera" class="qr-btn qr-btn-primary grow justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>
            </svg>
            Reset Kamera
        </button>
    </div>
</div>

{{-- TIPS BOX --}}
<div class="mt-5 p-4 rounded-2xl flex items-start gap-3"
     style="background:#eff6ff;border:1px solid #bfdbfe;">
    <div class="p-1.5 bg-blue-100 rounded-lg flex-shrink-0">
        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        </svg>
    </div>
    <div class="text-xs">
        <p class="font-black text-blue-900 mb-1.5">📱 Cara Menggunakan Scanner</p>
        <ul class="text-blue-700 space-y-1 list-disc ml-3">
            <li>Letakkan QR Code siswa di <strong>area tengah</strong> kamera.</li>
            <li>Pastikan pencahayaan <strong>cukup terang</strong>.</li>
            <li>Setelah terdeteksi, <strong>form input</strong> akan muncul otomatis.</li>
            <li>Bisa juga gunakan <strong>scanner barcode USB</strong> via tombol "Input NIS Manual".</li>
        </ul>
    </div>
</div>
