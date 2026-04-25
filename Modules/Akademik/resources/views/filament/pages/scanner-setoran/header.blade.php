{{-- Polling to keep stats fresh --}}
<span wire:poll.10s="loadStats" style="display:none;"></span>
<span wire:poll.10s="loadRecentScans" style="display:none;"></span>

{{-- HERO HEADER --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-500 shadow-lg p-6 mb-6 text-white flex flex-col md:flex-row items-center justify-between gap-4">
    <!-- background pattern -->
    <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative z-10 flex flex-col items-center md:items-start text-center md:text-left">
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight flex items-center gap-3">
            <svg class="w-8 h-8 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
            </svg>
            Scanner Setoran Ngaji
        </h1>
        <p class="mt-1 text-emerald-100 font-medium text-sm">Scan QR Code siswa untuk mencatat setoran harian & kehadiran kegiatan secara cepat & akurat.</p>
    </div>

    <div class="relative z-10 flex items-center gap-4">
        {{-- CLOCK --}}
        <div class="hidden md:block text-right mr-2">
            <div class="text-2xl font-black tabular-nums tracking-tighter text-white" x-text="currentTime.toLocaleTimeString('id-ID', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' })"></div>
            <div class="text-xs font-bold text-emerald-200 uppercase tracking-widest" x-text="currentTime.toLocaleDateString('id-ID', { weekday:'short', day:'numeric', month:'short', year:'numeric' })"></div>
        </div>

        {{-- STATUS --}}
        <div x-show="$wire.isOnline" class="flex items-center gap-2 bg-emerald-900/40 border border-emerald-400/30 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-emerald-100 backdrop-blur-md">
            <span class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span>
            </span>
            Online
        </div>
        <div x-show="!$wire.isOnline" x-cloak class="flex items-center gap-2 bg-red-900/40 border border-red-400/30 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest text-red-200 backdrop-blur-md">
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            Offline
        </div>

        {{-- FULLSCREEN --}}
        <button @click="toggleFullscreen()" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 transition text-white" title="Fullscreen">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0L15 15"/>
            </svg>
        </button>
    </div>
</div>
