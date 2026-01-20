<x-filament-panels::page>
    <style>
        .qr-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .qr-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .qr-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        .qr-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .dark .qr-card {
            background: #1f2937;
        }

        .qr-camera {
            position: relative;
            background: #111827;
            aspect-ratio: 16/9;
        }

        .qr-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .qr-pulse {
            position: relative;
            display: flex;
            height: 0.5rem;
            width: 0.5rem;
        }

        .qr-pulse-ring {
            position: absolute;
            display: inline-flex;
            height: 100%;
            width: 100%;
            border-radius: 9999px;
            background: #f87171;
            opacity: 0.75;
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        .qr-pulse-dot {
            position: relative;
            display: inline-flex;
            border-radius: 9999px;
            height: 0.5rem;
            width: 0.5rem;
            background: #ef4444;
        }

        @keyframes ping {

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .qr-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.2);
            /* Much lower opacity */
            backdrop-filter: blur(1px);
            /* minimal blur */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 10;
        }

        .qr-scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, transparent, #ef4444, transparent);
            animation: scan 3s ease-in-out infinite;
        }

        @keyframes scan {

            0%,
            100% {
                top: 0%;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                top: 100%;
                opacity: 0;
            }
        }

        .qr-actions {
            padding: 1rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .dark .qr-actions {
            background: rgba(31, 41, 55, 0.5);
            border-top-color: #374151;
        }

        .qr-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .qr-btn:hover {
            background: #f9fafb;
        }

        .qr-btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .qr-btn-primary:hover {
            background: #1d4ed8;
        }

        .qr-btn-danger {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
        }

        .qr-btn-danger:hover {
            background: #b91c1c;
        }

        .qr-btn-success {
            background: #16a34a;
            border-color: #16a34a;
            color: white;
        }

        .qr-btn-success:hover {
            background: #15803d;
        }

        .dark .qr-btn {
            background: #1f2937;
            border-color: #374151;
            color: #d1d5db;
        }

        .dark .qr-btn:hover {
            background: #374151;
        }

        .qr-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .qr-stat-card {
            background: #2563eb;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            transition: transform 0.3s;
        }

        .qr-stat-card:hover {
            transform: scale(1.05);
        }

        .qr-stat-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: white;
            transition: all 0.3s;
        }

        .qr-stat-label {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.25rem;
        }

        .qr-list-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dark .qr-list-header {
            border-bottom-color: #374151;
        }

        .qr-list-title {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
        }

        .dark .qr-list-title {
            color: white;
        }

        .qr-list-body {
            padding: 1rem;
            max-height: 400px;
            overflow-y: auto;
        }

        .qr-list-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            animation: slideIn 0.3s ease-out;
        }

        .dark .qr-list-item {
            background: rgba(31, 41, 55, 0.5);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .qr-empty {
            text-align: center;
            padding: 2rem 0;
        }

        .qr-info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .dark .qr-info-box {
            background: rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.2);
        }

        .qr-info-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 0.5rem;
        }

        .dark .qr-info-title {
            color: #93c5fd;
        }

        .qr-info-list {
            font-size: 0.75rem;
            color: #1e40af;
        }

        .dark .qr-info-list {
            color: #bfdbfe;
        }

        .qr-info-item {
            display: flex;
            align-items: start;
            gap: 0.5rem;
            margin-top: 0.375rem;
        }

        .qr-fullscreen-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 20;
        }

        .qr-admin-panel {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .dark .qr-admin-panel {
            background: rgba(251, 191, 36, 0.1);
            border-color: rgba(251, 191, 36, 0.3);
        }

        .qr-volume-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: #f3f4f6;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .dark .qr-volume-control {
            background: rgba(55, 65, 81, 0.5);
        }

        .qr-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .qr-status-online {
            background: #dcfce7;
            color: #166534;
        }

        .qr-status-offline {
            background: #fee2e2;
            color: #991b1b;
        }

        .qr-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            /* Transparent background to keep camera visible */
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .qr-modal-content {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }

        .dark .qr-modal-content {
            background: #1f2937;
        }

        .qr-countdown {
            font-size: 3rem;
            font-weight: 700;
            color: #2563eb;
            margin: 1rem 0;
        }

        [x-cloak] {
            display: none !important;
        }

        #reader {
            border: none !important;
        }

        #reader__scan_region,
        #reader__dashboard_section,
        #reader__header_message {
            display: none !important;
        }

        /* Fullscreen styles */
        .qr-fullscreen {
            position: fixed !important;
            inset: 0 !important;
            z-index: 9999 !important;
            background: #111827 !important;
        }

        .qr-fullscreen .qr-container {
            max-width: none !important;
            height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
        }
    </style>

    <div x-data="{
        mode: 'scan',
        scannedUser: null,
        errorMessage: '',
        currentTime: new Date(),
        isFullscreen: false,
        countdown: 10,
        showSuccessModal: false,
        offlineQueue: [],
        init() {
            setInterval(() => this.currentTime = new Date(), 1000);
            this.checkOnlineStatus();
            setInterval(() => this.checkOnlineStatus(), 5000);
            this.loadOfflineQueue();
    
            // Fullscreen change listener
            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
            });
        },
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        },
        checkOnlineStatus() {
            @this.isOnline = navigator.onLine;
            if (navigator.onLine && this.offlineQueue.length > 0) {
                this.syncOfflineQueue();
            }
        },
        loadOfflineQueue() {
            const queue = localStorage.getItem('qr_offline_queue');
            if (queue) {
                this.offlineQueue = JSON.parse(queue);
                @this.pendingScans = this.offlineQueue.length;
            }
        },
        saveOfflineQueue() {
            localStorage.setItem('qr_offline_queue', JSON.stringify(this.offlineQueue));
            @this.pendingScans = this.offlineQueue.length;
        },
        syncOfflineQueue() {
            // Sync offline scans when back online
            console.log('Syncing offline queue:', this.offlineQueue);
            const scansToSync = [...this.offlineQueue];
            this.offlineQueue = [];
            this.saveOfflineQueue();
    
            // Call batch sync
            @this.syncOfflineScans(scansToSync).then(() => {
                console.log('Sync complete');
            });
        }
    }" :class="{ 'qr-fullscreen': isFullscreen }">
        <!-- Hidden poll to keep stats updated without re-rendering whole UI -->
        <span wire:poll.5s="loadTodayStats"></span>
        <!-- Fullscreen Toggle Button -->
        <button @click="toggleFullscreen()" class="qr-fullscreen-btn qr-btn" title="Toggle Fullscreen (F11)">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
        </button>

        <div class="qr-container">
            <div class="qr-grid">

                {{-- Camera Section --}}
                <div>
                    <!-- Admin Control Panel -->
                    @if (auth()->user()->hasAnyRole(['super_admin', 'admin_unit']))
                        <div class="qr-admin-panel">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <h4 style="font-weight: 600; color: #92400e;">⚙️ Admin Controls</h4>
                                <span class="qr-status-badge"
                                    :class="$wire.scannerEnabled ? 'qr-status-online' : 'qr-status-offline'">
                                    <span x-text="$wire.scannerEnabled ? '● Active' : '● Disabled'"></span>
                                </span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                <button wire:click="toggleScanner" wire:key="btn-toggle-scanner"
                                    wire:loading.attr="disabled" class="qr-btn"
                                    :class="$wire.scannerEnabled ? 'qr-btn-danger' : 'qr-btn-success'">
                                    <span x-text="$wire.scannerEnabled ? 'Disable Scanner' : 'Enable Scanner'"></span>
                                </button>
                                @if (auth()->user()->hasRole('super_admin'))
                                    <button wire:click="toggleEmergencyOverride" wire:key="btn-toggle-emergency"
                                        wire:loading.attr="disabled" class="qr-btn"
                                        :class="$wire.emergencyOverride ? 'qr-btn-danger' : 'qr-btn'">
                                        <span
                                            x-text="$wire.emergencyOverride ? '🚨 Override ON' : 'Emergency Override'"></span>
                                    </button>
                                @endif
                                <a href="{{ route('filament.admin.resources.absensis.index') }}"
                                    class="qr-btn qr-btn-primary">
                                    📊 View Report
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Volume Control -->
                    <div class="qr-volume-control">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #6b7280;" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        </svg>
                        <input type="range" min="0" max="100" wire:model.live="volume" style="flex: 1;" />
                        <span style="font-size: 0.875rem; font-weight: 600; color: #374151; min-width: 3rem;"
                            x-text="$wire.volume + '%'"></span>
                    </div>

                    <!-- Scan Mode Selector -->
                    <!-- Scan Mode Selector (Redesigned) -->
                    <div
                        class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                        <div
                            class="grid grid-cols-2 p-1 gap-1 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <!-- Daily Mode Button -->
                            <button type="button" wire:click="$set('scanMode', 'daily')"
                                class="flex flex-col items-center justify-center p-3 rounded-lg transition-all duration-200 group relative overflow-hidden"
                                :class="$wire.scanMode === 'daily' ?
                                    'bg-white dark:bg-gray-800 shadow-md ring-1 ring-black/5' :
                                    'hover:bg-gray-200 dark:hover:bg-gray-800'">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 transition-opacity"
                                    :class="$wire.scanMode === 'daily' ? 'opacity-100' : ''"></div>
                                <span class="text-2xl mb-1 relative z-10">🗓️</span>
                                <span class="text-xs font-bold uppercase tracking-wider relative z-10"
                                    :class="$wire.scanMode === 'daily' ? 'text-blue-600 dark:text-blue-400' :
                                        'text-gray-500 dark:text-gray-400'">Absen
                                    Harian</span>
                            </button>

                            <!-- Event Mode Button -->
                            <button type="button" wire:click="$set('scanMode', 'event')"
                                class="flex flex-col items-center justify-center p-3 rounded-lg transition-all duration-200 group relative overflow-hidden"
                                :class="$wire.scanMode === 'event' ?
                                    'bg-white dark:bg-gray-800 shadow-md ring-1 ring-black/5' :
                                    'hover:bg-gray-200 dark:hover:bg-gray-800'">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent opacity-0 transition-opacity"
                                    :class="$wire.scanMode === 'event' ? 'opacity-100' : ''"></div>
                                <span class="text-2xl mb-1 relative z-10">🎉</span>
                                <span class="text-xs font-bold uppercase tracking-wider relative z-10"
                                    :class="$wire.scanMode === 'event' ? 'text-purple-600 dark:text-purple-400' :
                                        'text-gray-500 dark:text-gray-400'">Kegiatan
                                    / Event</span>
                            </button>
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
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                    <div class="qr-card">
                        <div class="qr-camera" wire:ignore>
                            <div id="reader" style="width: 100%; height: 100%;"></div>

                            <div class="qr-badge">
                                <span class="qr-pulse">
                                    <span class="qr-pulse-ring"></span>
                                    <span class="qr-pulse-dot"></span>
                                </span>
                                Camera Active
                            </div>

                            <!-- Connection Status Badge -->
                            <div style="position: absolute; top: 1rem; right: 1rem;">
                                <span class="qr-status-badge"
                                    :class="$wire.isOnline ? 'qr-status-online' : 'qr-status-offline'">
                                    <span x-text="$wire.isOnline ? '● Online' : '● Offline'"></span>
                                    <span x-show="$wire.pendingScans > 0"
                                        x-text="'(' + $wire.pendingScans + ')'"></span>
                                </span>
                            </div>

                            <div id="scan-overlay" class="qr-overlay">
                                <div
                                    style="padding: 1rem; background: rgba(255,255,255,0.1); border-radius: 9999px; margin-bottom: 0.75rem;">
                                    <svg style="width: 2.5rem; height: 2.5rem; color: #60a5fa;" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 style="font-size: 1.125rem; font-weight: 600; color: white;">Memverifikasi
                                    Lokasi...
                                </h3>
                                <p style="font-size: 0.875rem; color: #d1d5db; margin-top: 0.25rem;">Mohon tunggu
                                    sebentar</p>
                            </div>

                            <div class="qr-scan-line"></div>
                        </div>

                        <div class="qr-actions">
                            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                                <button type="button"
                                    x-on:click="$dispatch('open-modal', { id: 'manual-input-modal' })"
                                    class="qr-btn qr-btn-primary">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Input NIP Manual
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Section --}}
                <div>
                    <div class="qr-stats">
                        <div class="qr-stat-card">
                            <div class="qr-stat-value" x-text="$wire.todayStats.checkedIn"></div>
                            <div class="qr-stat-label">Hadir Masuk</div>
                        </div>
                        <div class="qr-stat-card">
                            <div class="qr-stat-value" x-text="$wire.todayStats.checkedOut"></div>
                            <div class="qr-stat-label">Sudah Pulang</div>
                        </div>
                    </div>

                    <div class="qr-card" style="margin-bottom: 1.5rem;">
                        <div class="qr-list-header">
                            <h3 class="qr-list-title">Riwayat Hari Ini</h3>
                            <span style="font-size: 0.75rem; color: #6b7280;">Real-time</span>
                        </div>

                        <div class="qr-list-body">
                            <template x-if="$wire.recentScans.length === 0">
                                <div class="qr-empty">
                                    <svg style="width: 4rem; height: 4rem; color: #9ca3af; margin: 0 auto 0.75rem;"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <p style="font-size: 0.875rem; color: #6b7280;">Belum ada scan hari ini...</p>
                                </div>
                            </template>

                            <template x-for="scan in $wire.recentScans" :key="scan.timestamp">
                                <div class="qr-list-item">
                                    <img :src="scan.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(scan.name)}`"
                                        style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                            x-text="scan.name"></p>
                                        <p style="font-size: 0.75rem; color: #6b7280;"
                                            x-text="scan.time + ' • ' + scan.timestamp"></p>
                                    </div>
                                    <span
                                        style="display: inline-flex; align-items: center; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;"
                                        :style="scan.type === 'check-in' ? 'background: #dcfce7; color: #166534;' :
                                            'background: #dbeafe; color: #1e40af;'"
                                        x-text="scan.type === 'check-in' ? 'IN' : 'OUT'">
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="qr-info-box">
                        <h4 class="qr-info-title">💡 Petunjuk Penggunaan:</h4>
                        <div class="qr-info-list">
                            <div class="qr-info-item">
                                <span>•</span>
                                <span>Pastikan pencahayaan cukup terang</span>
                            </div>
                            <div class="qr-info-item">
                                <span>•</span>
                                <span>Tahan QR code stabil di depan kamera</span>
                            </div>
                            <div class="qr-info-item">
                                <span>•</span>
                                <span>Tunggu konfirmasi sukses</span>
                            </div>
                            <div class="qr-info-item">
                                <span>•</span>
                                <span>Tekan F11 untuk fullscreen mode</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal with Countdown -->
        <div x-show="showSuccessModal" x-cloak class="qr-modal-overlay"
            @click="showSuccessModal = false; scannedUser = null">
            <div class="qr-modal-content" @click.stop>
                <template x-if="scannedUser">
                    <div>
                        <div style="position: relative; display: inline-block; margin-bottom: 1.5rem;">
                            <div style="position: absolute; inset: 0; border-radius: 9999px; blur: 1rem; opacity: 0.4; animation: pulse 2s infinite;"
                                :style="scannedUser.type === 'check-in' ? 'background: #10b981;' : 'background: #3b82f6;'">
                            </div>
                            <img :src="scannedUser.avatar"
                                style="position: relative; width: 12rem; height: 12rem; border-radius: 9999px; object-fit: cover; border: 4px solid white;">
                            <div style="position: absolute; bottom: 0; right: 0; width: 3rem; height: 3rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; border: 3px solid white;"
                                :style="scannedUser.type === 'check-in' ? 'background: #10b981;' : 'background: #3b82f6;'">
                                <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;"
                            x-text="scannedUser.name"></h2>
                        <p style="font-size: 1rem; color: #6b7280; margin-bottom: 1rem;" x-text="scannedUser.email">
                        </p>
                        <div style="display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; border-radius: 9999px; font-size: 1.125rem; font-weight: 700; margin-bottom: 1.5rem;"
                            :style="scannedUser.type === 'check-in' ? 'background: #dcfce7; color: #166534;' :
                                'background: #dbeafe; color: #1e40af;'">
                            <span
                                x-text="scannedUser.type === 'check-in' ? '✓ CHECK-IN BERHASIL' : '✓ CHECK-OUT BERHASIL'"></span>
                        </div>
                        <div class="qr-countdown" x-text="countdown"></div>
                        <p style="font-size: 0.875rem; color: #9ca3af;">Menutup otomatis...</p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Manual Input Modal --}}
        <x-filament::modal id="manual-input-modal" width="md">
            <x-slot name="heading">Input NIP Manual</x-slot>
            <div style="padding: 1.5rem 0;">
                <label
                    style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                    Nomor Induk Pegawai (NIP)
                </label>
                <input type="text" id="manual-token" placeholder="Masukkan NIP..."
                    style="width: 100%; padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; text-align: center; font-size: 1.125rem; font-family: monospace;">
                <p style="font-size: 0.75rem; color: #6b7280; text-align: center; margin-top: 0.75rem;">
                    Pastikan NIP yang dimasukkan sudah benar
                </p>
            </div>
            <x-slot name="footerActions">
                <button type="button" x-on:click="$dispatch('close-modal', { id: 'manual-input-modal' })"
                    class="qr-btn">Batal</button>
                <button type="button" id="manual-submit" class="qr-btn qr-btn-primary">Submit</button>
            </x-slot>
        </x-filament::modal>

        <audio id="beep-checkin" preload="auto">
            <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
        </audio>
        <audio id="beep-checkout" preload="auto">
            <source src="https://assets.mixkit.co/active_storage/sfx/2018/2018-preview.mp3" type="audio/mpeg">
        </audio>
        <audio id="beep-error" preload="auto">
            <source src="https://assets.mixkit.co/active_storage/sfx/2863/2863-preview.mp3" type="audio/mpeg">
        </audio>

        @push('scripts')
            <script src="https://unpkg.com/html5-qrcode"></script>
            <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const html5QrCode = new Html5Qrcode("reader");
                    let isScanning = false;

                    const startScanner = () => {
                        html5QrCode.start({
                                facingMode: "environment"
                            }, {
                                fps: 24,
                                qrbox: {
                                    width: 450,
                                    height: 450
                                }
                            },
                            (decodedText) => {
                                if (isScanning) return;

                                // Offline Check
                                if (!navigator.onLine) {
                                    handleOfflineScan(decodedText);
                                    return;
                                }

                                isScanning = true;
                                document.getElementById('scan-overlay').style.opacity = '1';

                                setTimeout(() => {
                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(
                                            (pos) => {
                                                document.getElementById('scan-overlay').style.opacity =
                                                    '0';
                                                processScan(decodedText, pos.coords.latitude, pos.coords
                                                    .longitude);
                                            },
                                            (err) => {
                                                document.getElementById('scan-overlay').style.opacity =
                                                    '0';
                                                processScan(decodedText, null, null);
                                            }, {
                                                timeout: 3000,
                                                enableHighAccuracy: true
                                            }
                                        );
                                    } else {
                                        document.getElementById('scan-overlay').style.opacity = '0';
                                        processScan(decodedText, null, null);
                                    }
                                }, 300);
                            }
                        ).catch(err => {
                            console.error("Camera Error:", err);
                            // Auto restart if camera fails
                            setTimeout(startScanner, 2000);
                        });
                    };

                    const handleOfflineScan = (token) => {
                        const modal = document.querySelector('[x-data]').__x.$data;
                        const scan = {
                            token: token,
                            time: new Date().toISOString(),
                            lat: null,
                            lng: null
                        };
                        modal.offlineQueue.push(scan);
                        modal.saveOfflineQueue();

                        // Fake success for offline user
                        window.dispatchEvent(new CustomEvent('scan-success', {
                            detail: {
                                name: 'Offline Saved',
                                email: 'Sync waiting...',
                                avatar: null,
                                type: 'offline'
                            }
                        }));
                    };

                    const processScan = (token, lat, lng) => {
                        @this.processScan(token, lat, lng).then(() => {
                            // Quick lockout for fast scanning (1.5s)
                            setTimeout(() => isScanning = false, 1500);
                        }).catch(() => {
                            isScanning = false;
                        });
                    };

                    const manualSubmit = document.getElementById('manual-submit');
                    if (manualSubmit) {
                        manualSubmit.addEventListener('click', () => {
                            const token = document.getElementById('manual-token').value.trim();
                            if (token) {
                                window.dispatchEvent(new CustomEvent('close-modal', {
                                    detail: {
                                        id: 'manual-input-modal'
                                    }
                                }));
                                document.getElementById('manual-token').value = '';
                                processScan(token, null, null);
                            }
                        });
                    }

                    const manualTokenInput = document.getElementById('manual-token');
                    if (manualTokenInput) {
                        manualTokenInput.addEventListener('keypress', (e) => {
                            if (e.key === 'Enter') manualSubmit.click();
                        });
                    }

                    // F11 for fullscreen
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'F11') {
                            e.preventDefault();
                            if (!document.fullscreenElement) {
                                document.documentElement.requestFullscreen();
                            } else {
                                document.exitFullscreen();
                            }
                        }
                    });

                    // Prevent multiple starts on re-renders
                    if (window.html5QrCode) {
                        window.html5QrCode.stop().then(() => {
                            startScanner();
                        }).catch(() => startScanner());
                    } else {
                        window.html5QrCode = html5QrCode;
                        startScanner();
                    }
                });

                window.addEventListener('scan-success', (event) => {
                    const scannedUser = {
                        name: event.detail.name,
                        email: event.detail.email,
                        avatar: event.detail.avatar,
                        type: event.detail.type
                    };

                    Alpine.store('scanner', scannedUser);

                    // Play appropriate sound
                    const volume = @this.volume / 100;
                    const audio = event.detail.type === 'check-in' ?
                        document.getElementById('beep-checkin') :
                        document.getElementById('beep-checkout');

                    if (audio) {
                        audio.volume = volume;
                        audio.play();
                    }

                    // No visual effects (modal/confetti) as requested.
                });

                window.addEventListener('scan-error', (event) => {
                    const volume = @this.volume / 100;
                    const audio = document.getElementById('beep-error');
                    if (audio) {
                        audio.volume = volume;
                        audio.play();
                    }
                });
            </script>
        @endpush
    </div>
</x-filament-panels::page>
