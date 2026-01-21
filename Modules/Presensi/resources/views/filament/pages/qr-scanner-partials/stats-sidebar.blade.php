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
                                <span>Tekan F11 for fullscreen mode</span>
                            </div>
                        </div>
                    </div>
                </div>
