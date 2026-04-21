<aside class="space-y-5">

    {{-- ===== STATS ===== --}}
    <div class="qr-stat-grid">

        <div class="qr-stat-card qr-stat-total">
            <div class="relative z-10">
                <p class="text-4xl font-black leading-none" x-text="$wire.todayStats.total ?? 0"></p>
                <p class="text-[10px] font-bold uppercase tracking-[.12em] opacity-75 mt-1">Setoran Hari Ini</p>
            </div>
            {{-- Watermark icon --}}
            <svg class="absolute -bottom-3 -right-3 w-20 h-20 opacity-[.12]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.94 49.94 0 00-9.945 2.577l-1.092.364V21.5h-1.5V12.998l-1.092-.364a49.94 49.94 0 00-9.945-2.577.75.75 0 01-.231-1.337A60.65 60.65 0 0111.7 2.805z"/>
                <path d="M13.06 15.473a48.45 48.45 0 017.613-2.183l.115-.025a.5.5 0 01.607.486v2.548a49.62 49.62 0 00-9.145 2.654.498.498 0 01-.45 0c-2.315-1-4.66-1.838-7.017-2.502a.5.5 0 01-.363-.483V13.72a.5.5 0 01.62-.486 48.434 48.434 0 017.74 2.239l.89.3z"/>
            </svg>
        </div>

        <div class="qr-stat-card qr-stat-avg">
            <div class="relative z-10">
                <p class="text-4xl font-black leading-none" x-text="$wire.todayStats.countA ?? 0"></p>
                <p class="text-[10px] font-bold uppercase tracking-[.12em] opacity-75 mt-1">Sangat Lancar (A)</p>
            </div>
            <svg class="absolute -bottom-3 -right-3 w-20 h-20 opacity-[.12]" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/>
            </svg>
        </div>

    </div>

    {{-- ===== RECENT SCANS ===== --}}
    <div class="qr-card">
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="font-black text-gray-800 dark:text-white text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Hari Ini
            </h3>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"
                  x-text="$wire.recentScans.length + ' data'"></span>
        </div>

        <div class="p-4 max-h-72 overflow-y-auto space-y-2" id="history-list">

            <template x-for="scan in $wire.recentScans" :key="scan.id">
                <div class="qr-history-item">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0"
                         style="background:linear-gradient(135deg,#eff6ff,#dbeafe);color:#2563eb;"
                         x-text="scan.name ? scan.name.charAt(0).toUpperCase() : '?'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="scan.name"></p>
                        <p class="text-[11px] text-gray-400 truncate" x-text="scan.materi"></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[10px] text-gray-400 font-bold mb-0.5" x-text="scan.time"></p>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black"
                              :class="{
                                  'bg-emerald-100 text-emerald-700': scan.grade === 'A',
                                  'bg-blue-100 text-blue-700':      scan.grade === 'B',
                                  'bg-rose-100 text-rose-700':      scan.grade === 'C'
                              }"
                              x-text="scan.grade">
                        </span>
                    </div>
                </div>
            </template>

            <template x-if="!$wire.recentScans || $wire.recentScans.length === 0">
                <div class="py-10 text-center">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <p class="text-xs text-gray-400 font-bold">Belum ada setoran hari ini</p>
                </div>
            </template>

        </div>
    </div>

    {{-- ===== VOLUME ===== --}}
    <div class="qr-card p-4">
        <div class="flex items-center gap-3">
            <svg x-show="$wire.volume > 0" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/>
            </svg>
            <svg x-show="$wire.volume == 0" x-cloak class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6.75h-2.25a2.25 2.25 0 00-2.25 2.25v3a2.25 2.25 0 002.25 2.25h2.25l4.5 4.5V2.25l-4.5 4.5z"/>
            </svg>
            <div class="flex-1">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Volume Suara</p>
                <input type="range" wire:model.live="volume" min="0" max="100"
                       class="w-full h-1.5 rounded-full accent-blue-600 cursor-pointer">
            </div>
            <span class="text-xs font-black text-gray-500 w-7 text-right" x-text="$wire.volume + '%'"></span>
        </div>
    </div>

</aside>
