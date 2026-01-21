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
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                    Mode standar untuk mencatat jam <b>Masuk</b> dan <b>Pulang</b> harian pegawai.
                                </p>
                            </div>

                            <div x-show="$wire.scanMode === 'event'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <label class="block mb-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    Pilih Kegiatan Hari Ini
                                </label>
                                <div class="relative">
                                    <select wire:model.live="selectedEventId"
                                        class="appearance-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-3 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500 transition-shadow shadow-sm hover:shadow-md">
                                        <option value="">-- Pilih Event --</option>
                                        @foreach ($this->events as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <!-- Fixed size for the chevron icon -->
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @if (empty($this->events))
                                    <div
                                        class="mt-2 flex items-center gap-2 text-amber-600 bg-amber-50 p-2 rounded-md border border-amber-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-xs font-medium">Tidak ada kegiatan aktif hari ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
