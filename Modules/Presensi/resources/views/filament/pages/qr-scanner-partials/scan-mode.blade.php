                    <!-- Scan Mode Selector -->
                    <div class="qr-segment-box">
                        <div class="qr-segment-header">
                            <div class="qr-segment-track">
                                <!-- Sliding Active Indicator -->
                                <div class="qr-segment-indicator"
                                    :style="$wire.scanMode === 'event' ? 'transform: translateX(100%)' :
                                        'transform: translateX(0)'">
                                </div>

                                <!-- Daily Mode Button -->
                                <button type="button" wire:click="$set('scanMode', 'daily')" class="qr-segment-btn"
                                    :class="$wire.scanMode === 'daily' ? 'active' : ''">
                                    <span>🗓️</span>
                                    <span>Absen Harian</span>
                                </button>

                                <!-- Event Mode Button -->
                                <button type="button" wire:click="$set('scanMode', 'event')" class="qr-segment-btn"
                                    :class="$wire.scanMode === 'event' ? 'active-purple' : ''">
                                    <span>🎉</span>
                                    <span>Kegiatan / Event</span>
                                </button>
                            </div>
                        </div>

                        <!-- Description & Controls -->
                        <div class="p-4 bg-white dark:bg-gray-800">
                            <div x-show="$wire.scanMode === 'daily'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0">
                            </div>

                            <div x-show="$wire.scanMode === 'event'"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-2 blur-sm"
                                x-transition:enter-end="opacity-100 translate-y-0 blur-0">

                                <div class="mb-3 flex items-center justify-center gap-2">
                                    <div
                                        class="h-px flex-1 bg-gradient-to-r from-transparent via-purple-300 to-transparent">
                                    </div>
                                    <label
                                        class="px-2 text-[10px] uppercase tracking-widest font-bold text-purple-600 dark:text-purple-400">
                                        Pilih Kegiatan Aktif
                                    </label>
                                    <div
                                        class="h-px flex-1 bg-gradient-to-r from-transparent via-purple-300 to-transparent">
                                    </div>
                                </div>

                                <div class="relative group">
                                    <div
                                        class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl blur opacity-10 group-hover:opacity-20 transition duration-300">
                                    </div>
                                    <div class="relative">
                                        <select wire:model.live="selectedEventId"
                                            class="appearance-none w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-2 border-purple-100 dark:border-purple-900/30 text-gray-900 dark:text-white text-base font-medium rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 block p-4 pr-12 transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer">
                                            <option value="">Ketuk untuk Memilih Event</option>
                                            @foreach ($this->events as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-purple-600 dark:text-purple-400">
                                            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                @if (empty($this->events))
                                    <div
                                        class="mt-4 p-4 rounded-xl bg-amber-50/50 dark:bg-amber-900/20 backdrop-blur-sm border border-amber-200/50 dark:border-amber-800/30 flex items-center gap-3">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                                            <span class="text-xl">⚠️</span>
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs font-bold text-amber-800 dark:text-amber-200 uppercase tracking-tight">
                                                Perhatian</p>
                                            <p class="text-[11px] text-amber-700/80 dark:text-amber-300/60">Tidak ada
                                                kegiatan yang dijadwalkan hari ini.</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3 flex items-center justify-center">
                                        <span class="flex h-2 w-2 relative">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                            <span
                                                class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                                        </span>
                                        <span class="ml-2 text-[10px] text-gray-400 font-medium italic">Silakan pilih
                                            salah satu event di atas</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
