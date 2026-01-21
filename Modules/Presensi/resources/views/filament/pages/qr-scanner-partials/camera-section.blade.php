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
