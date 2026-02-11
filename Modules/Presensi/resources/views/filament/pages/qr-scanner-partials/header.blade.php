        <!-- Hidden poll to keep stats updated without re-rendering whole UI -->
        <span wire:poll.5s="refreshScannerData"></span>

        <div class="qr-header-clock" style="width: 100%; text-align: center; padding: 1.5rem 0 0.5rem;">
            <div style="font-weight: 800; letter-spacing: -0.05em; line-height: 1;"
                class="text-3xl md:text-5xl text-gray-900 dark:text-white"
                x-text="currentTime.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' })">
            </div>
            <div style="font-weight: 600; margin-top: 0.5rem;"
                class="text-sm md:text-base text-gray-500 dark:text-gray-400 uppercase tracking-widest"
                x-text="currentTime.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })">
            </div>
        </div>

        <!-- Fullscreen Toggle Button -->
        <button @click="toggleFullscreen()" class="qr-fullscreen-btn qr-btn" title="Toggle Fullscreen (F11)">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
        </button>
