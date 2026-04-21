{{-- Polling to keep stats fresh --}}
<span wire:poll.10s="loadStats" style="display:none;"></span>
<span wire:poll.10s="loadRecentScans" style="display:none;"></span>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
            <span class="p-1.5 bg-blue-100 dark:bg-blue-900/40 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
                </svg>
            </span>
            Scanner Setoran Ngaji
        </h1>
        <p class="text-sm text-gray-400 mt-1 ml-10">Scan QR Code siswa untuk mencatat setoran harian secara cepat.</p>
    </div>

    <div class="flex items-center gap-3">
        {{-- CLOCK --}}
        <div class="text-right hidden md:block">
            <div class="font-black text-gray-800 dark:text-white text-lg"
                 x-text="currentTime.toLocaleTimeString('id-ID', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' })"></div>
            <div class="text-[10px] uppercase tracking-widest text-gray-400 font-bold"
                 x-text="currentTime.toLocaleDateString('id-ID', { weekday:'short', day:'numeric', month:'short', year:'numeric' })"></div>
        </div>

        {{-- STATUS BADGE --}}
        <div x-show="$wire.isOnline"
             class="hidden md:flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-[11px] font-black border border-emerald-200">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse inline-block"></span> ONLINE
        </div>
        <div x-show="!$wire.isOnline" x-cloak
             class="hidden md:flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-700 rounded-full text-[11px] font-black border border-rose-200">
            <span class="w-1.5 h-1.5 bg-rose-500 rounded-full inline-block"></span> OFFLINE
        </div>

        {{-- FULLSCREEN --}}
        <button @click="toggleFullscreen()"
                class="p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-blue-100 hover:text-blue-600 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0L15 15"/>
            </svg>
        </button>
    </div>
</div>
