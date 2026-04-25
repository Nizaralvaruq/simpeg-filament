<aside class="space-y-6">

    {{-- ===== STATS ROW ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-5 text-white shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="relative z-10">
                <p class="text-4xl font-extrabold tracking-tight" x-text="$wire.todayStats.total ?? 0"></p>
                <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider mt-1 opacity-90">Setoran Hari Ini</p>
            </div>
            <svg class="absolute -bottom-4 -right-4 w-24 h-24 text-white/10" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.94 49.94 0 00-9.945 2.577l-1.092.364V21.5h-1.5V12.998l-1.092-.364a49.94 49.94 0 00-9.945-2.577.75.75 0 01-.231-1.337A60.65 60.65 0 0111.7 2.805z"/>
                <path d="M13.06 15.473a48.45 48.45 0 017.613-2.183l.115-.025a.5.5 0 01.607.486v2.548a49.62 49.62 0 00-9.145 2.654.498.498 0 01-.45 0c-2.315-1-4.66-1.838-7.017-2.502a.5.5 0 01-.363-.483V13.72a.5.5 0 01.62-.486 48.434 48.434 0 017.74 2.239l.89.3z"/>
            </svg>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 p-5 text-white shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="relative z-10">
                <p class="text-4xl font-extrabold tracking-tight" x-text="$wire.todayStats.countA ?? 0"></p>
                <p class="text-xs font-bold text-blue-100 uppercase tracking-wider mt-1 opacity-90">Sangat Lancar (A)</p>
            </div>
            <svg class="absolute -bottom-4 -right-4 w-24 h-24 text-white/10" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/>
            </svg>
        </div>

    </div>

    {{-- ===== RECENT SCANS ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-[320px]">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between shrink-0">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Hari Ini
            </h3>
            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-widest bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full"
                  x-text="$wire.recentScans.length + ' setoran'"></span>
        </div>

        <div class="flex-1 p-3 overflow-y-auto" id="history-list">

            <template x-for="scan in $wire.recentScans" :key="scan.id">
                <div class="flex items-center gap-3 p-3 mb-2 rounded-xl border border-gray-50 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800 hover:border-emerald-200 hover:bg-emerald-50 dark:hover:border-emerald-800 dark:hover:bg-emerald-900/20 transition-colors">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300"
                         x-text="scan.name ? scan.name.charAt(0).toUpperCase() : '?'">
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate" x-text="scan.name"></p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5" x-text="scan.materi"></p>
                    </div>
                    {{-- Time & Grade --}}
                    <div class="text-right shrink-0 flex flex-col items-end gap-1.5">
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold" x-text="scan.time"></p>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-xs font-black"
                              :class="{
                                  'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400': scan.grade === 'A',
                                  'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400': scan.grade === 'B',
                                  'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400': scan.grade === 'C'
                              }"
                              x-text="scan.grade">
                        </span>
                    </div>
                </div>
            </template>

            <template x-if="!$wire.recentScans || $wire.recentScans.length === 0">
                <div class="py-12 flex flex-col items-center justify-center text-center h-full">
                    <div class="w-16 h-16 mb-4 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Belum ada setoran</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Scan QR Code siswa untuk memulai</p>
                </div>
            </template>

        </div>
    </div>

    {{-- ===== VOLUME ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between gap-4 p-4">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-0">
                <svg x-show="$wire.volume > 0" class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/>
                </svg>
                <svg x-show="$wire.volume == 0" x-cloak class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6.75h-2.25a2.25 2.25 0 00-2.25 2.25v3a2.25 2.25 0 002.25 2.25h2.25l4.5 4.5V2.25l-4.5 4.5z"/>
                </svg>
                Volume
            </p>
            <div class="flex items-center gap-3 flex-1 justify-end max-w-[150px]">
                <input type="range" wire:model.live="volume" min="0" max="100"
                       class="flex-1 h-1.5 rounded-full accent-emerald-500 bg-gray-200 dark:bg-gray-700 cursor-pointer">
                <span class="text-xs font-bold text-gray-600 dark:text-gray-300 w-8 text-right tabular-nums"
                      x-text="$wire.volume + '%'"></span>
            </div>
        </div>
    </div>

    {{-- ===== TIPS BOX ===== --}}
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <div class="p-1.5 bg-emerald-500 rounded-lg shrink-0 mt-0.5 shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-900 dark:text-emerald-300 mb-1.5">Cara Menggunakan Scanner</p>
                <ul class="text-[11px] text-emerald-800 dark:text-emerald-400/80 space-y-1.5 list-disc ml-3 leading-relaxed">
                    <li>Arahkan <strong>QR Code siswa</strong> ke area kotak di tengah kamera.</li>
                    <li>Pastikan pencahayaan <strong>cukup terang</strong> untuk hasil optimal.</li>
                    <li>Form input akan muncul <strong>otomatis</strong> setelah QR terdeteksi.</li>
                    <li>Gunakan tombol <strong>Input NIS Manual</strong> untuk scanner barcode USB.</li>
                </ul>
            </div>
        </div>
    </div>

</aside>
