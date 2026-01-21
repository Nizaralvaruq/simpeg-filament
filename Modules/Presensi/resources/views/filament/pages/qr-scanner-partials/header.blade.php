        <!-- Hidden poll to keep stats updated without re-rendering whole UI -->
        <span wire:poll.5s="loadTodayStats"></span>
        <!-- Fullscreen Toggle Button -->
        <button @click="toggleFullscreen()" class="qr-fullscreen-btn qr-btn" title="Toggle Fullscreen (F11)">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
        </button>
