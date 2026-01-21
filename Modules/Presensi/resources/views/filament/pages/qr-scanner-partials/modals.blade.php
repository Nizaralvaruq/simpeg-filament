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
