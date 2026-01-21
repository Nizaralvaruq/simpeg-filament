                    <!-- Admin Control Panel (Relocated & Styled) -->
                    @if (auth()->user()->hasAnyRole(['super_admin', 'admin_unit']))
                        <div class="qr-admin-box">
                            <div class="qr-admin-header">
                                <h4 style="font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 0.5rem;"
                                    class="dark:text-white">
                                    <span>⚙️</span> Admin Controls
                                </h4>
                                <span class="qr-status-badge"
                                    :class="$wire.scannerEnabled ? 'qr-status-online' : 'qr-status-offline'">
                                    <span x-text="$wire.scannerEnabled ? '● Active' : '● Disabled'"></span>
                                </span>
                            </div>
                            <div class="qr-admin-grid">
                                <button wire:click="toggleScanner" wire:key="btn-toggle-scanner"
                                    wire:loading.attr="disabled" class="qr-btn"
                                    style="width: 100%; justify-content: center;"
                                    :class="$wire.scannerEnabled ? 'qr-btn-danger' : 'qr-btn-success'">
                                    <span x-text="$wire.scannerEnabled ? 'Disable' : 'Enable Scanner'"></span>
                                </button>

                                <a href="{{ route('filament.admin.resources.absensis.index') }}" class="qr-btn"
                                    style="width: 100%; justify-content: center; background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;">
                                    <span>📊</span> Report
                                </a>

                                @if (auth()->user()->hasRole('super_admin'))
                                    <button wire:click="toggleEmergencyOverride" wire:key="btn-toggle-emergency"
                                        wire:loading.attr="disabled" class="qr-btn"
                                        style="grid-column: span 2; justify-content: center;"
                                        :class="$wire.emergencyOverride ? 'qr-btn-danger' : 'qr-btn'">
                                        <span
                                            x-text="$wire.emergencyOverride ? '🚨 Emergency Override' : '⚠️ Override'"></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
