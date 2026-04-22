{{-- Polling to keep stats fresh --}}
<span wire:poll.10s="loadStats" style="display:none;"></span>
<span wire:poll.10s="loadRecentScans" style="display:none;"></span>

{{-- HERO HEADER --}}
<div class="qr-hero">
    <div class="qr-hero-left">
        <div class="qr-hero-title">
            <svg class="w-6 h-6 text-cyan-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
            </svg>
            Scanner Setoran Ngaji
        </div>
        <p class="qr-hero-sub">Scan QR Code siswa untuk mencatat setoran Al-Qur'an harian secara cepat & akurat.</p>
    </div>

    <div class="qr-hero-right">
        {{-- CLOCK --}}
        <div class="qr-clock-box hidden md:block">
            <div class="qr-clock-time"
                 x-text="currentTime.toLocaleTimeString('id-ID', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' })"></div>
            <div class="qr-clock-date"
                 x-text="currentTime.toLocaleDateString('id-ID', { weekday:'short', day:'numeric', month:'short', year:'numeric' })"></div>
        </div>

        {{-- STATUS --}}
        <div class="qr-online-badge" x-show="$wire.isOnline">
            <span class="qr-online-dot"></span> Online
        </div>
        <div x-show="!$wire.isOnline" x-cloak
             class="qr-online-badge" style="background:rgba(239,68,68,.15);color:#fca5a5;border-color:rgba(239,68,68,.3);">
            <span class="qr-online-dot" style="background:#f87171;"></span> Offline
        </div>

        {{-- FULLSCREEN --}}
        <button @click="toggleFullscreen()" class="qr-fullscreen-btn" title="Fullscreen">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0L15 15"/>
            </svg>
        </button>
    </div>
</div>
