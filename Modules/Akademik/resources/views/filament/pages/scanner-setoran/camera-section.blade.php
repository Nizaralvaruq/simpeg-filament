<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    {{-- SCAN MODE SETTINGS --}}
    <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Pengaturan Mode Scan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Tipe Scan</label>
                <select wire:model.live="scanMode" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2.5 transition-shadow shadow-sm">
                    <option value="setoran">Input Setoran Ngaji</option>
                    <option value="kehadiran">Kehadiran Kegiatan</option>
                </select>
            </div>
            
            <div x-data="{ mode: @entangle('scanMode') }" x-show="mode === 'kehadiran'" style="display: none;">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Pilih Kegiatan (Hari Ini)</label>
                <select wire:model="kegiatanId" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2.5 transition-shadow shadow-sm">
                    <option value="">-- Pilih Kegiatan --</option>
                    @foreach($availableKegiatans as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
                @if(empty($availableKegiatans))
                    <p class="text-xs text-rose-500 mt-1.5 font-medium">Tidak ada kegiatan yang terbuka hari ini.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- CAMERA VIEWPORT --}}
    <div class="relative bg-slate-900 aspect-4/3 md:aspect-video overflow-hidden group" wire:ignore>
        <div id="reader" class="w-full h-full [&>video]:object-cover [&>video]:w-full [&>video]:h-full [&>#reader__scan_region]:hidden [&>#reader__dashboard]:hidden"></div>

        {{-- Live indicator --}}
        <div class="absolute top-4 left-4 z-10 flex items-center gap-2 px-3 py-1.5 bg-black/40 backdrop-blur-md rounded-full border border-white/10 shadow-lg">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-bold text-white uppercase tracking-wider">Kamera Aktif</span>
        </div>

        {{-- Scan frame corners --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50%] aspect-square z-10 pointer-events-none">
            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-emerald-400 rounded-tl-xl transition-all duration-300 group-hover:scale-110 group-hover:-translate-x-1 group-hover:-translate-y-1"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-emerald-400 rounded-tr-xl transition-all duration-300 group-hover:scale-110 group-hover:translate-x-1 group-hover:-translate-y-1"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-emerald-400 rounded-bl-xl transition-all duration-300 group-hover:scale-110 group-hover:-translate-x-1 group-hover:translate-y-1"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-emerald-400 rounded-br-xl transition-all duration-300 group-hover:scale-110 group-hover:translate-x-1 group-hover:translate-y-1"></div>
        </div>

        {{-- Scan animation line --}}
        <div class="qr-scan-line"></div>

        {{-- Loading overlay --}}
        <div id="scan-loading" class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-slate-900/80 z-20 opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="w-12 h-12 border-4 border-emerald-400/30 border-t-emerald-400 rounded-full animate-spin"></div>
            <span class="text-emerald-400 font-extrabold text-sm tracking-widest uppercase shadow-sm">Mencari Data...</span>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex flex-col sm:flex-row gap-3 p-5 bg-gray-50 border-t border-gray-100 dark:bg-gray-800/80 dark:border-gray-700">
        <button type="button"
                @click="$dispatch('open-modal', { id: 'manual-input-modal' })"
                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-emerald-600 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-emerald-400 transition-all duration-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
            Input NIS Manual
        </button>

        <button type="button" id="btn-start-camera" 
                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-500 rounded-xl hover:from-emerald-500 hover:to-teal-400 focus:ring-4 focus:ring-emerald-200 dark:focus:ring-emerald-900 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            Reset Kamera
        </button>
    </div>

</div>
