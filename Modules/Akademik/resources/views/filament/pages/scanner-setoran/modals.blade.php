{{-- === SETORAN INPUT MODAL === --}}
<div x-show="$wire.showFormModal" x-cloak class="qr-modal-overlay" @keydown.escape.window="$wire.showFormModal = false">
    <div class="qr-modal-card" @click.away="$wire.showFormModal = false">

        {{-- MODAL HEADER --}}
        <div class="qr-modal-header">
            <div class="qr-modal-avatar">
                <img :src="$wire.scannedUser?.avatar" class="w-full h-full object-cover"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span style="display:none; align-items:center; justify-content:center; width:100%; height:100%; font-size:1.25rem; font-weight:900;"
                      x-text="$wire.scannedUser?.name?.charAt(0)?.toUpperCase() ?? '?'"></span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="px-2 py-0.5 bg-white/20 text-white/90 text-[9px] font-black rounded-md tracking-widest border border-white/25">SISWA</span>
                    <h2 class="qr-modal-name truncate" x-text="$wire.scannedUser?.name"></h2>
                </div>
                <p class="qr-modal-meta">
                    NIS: <span class="font-mono font-bold text-white" x-text="$wire.scannedUser?.nis"></span>
                    &nbsp;·&nbsp; Kelas <span x-text="$wire.scannedUser?.kelas"></span>
                </p>
            </div>
            <button @click="$wire.showFormModal = false" class="qr-modal-close">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- RIWAYAT TERAKHIR --}}
        <div x-show="$wire.riwayatTerakhir" x-cloak class="qr-modal-last">
            <span class="qr-badge-last">Terakhir</span>
            <span class="text-amber-900 font-semibold text-xs"
                  x-text="$wire.riwayatTerakhir?.tanggal + ' — ' + $wire.riwayatTerakhir?.materi"></span>
        </div>

        {{-- FORM --}}
        <form wire:submit.prevent="saveSetoran" class="qr-modal-form space-y-4">

            {{-- ROW 1: Jenis & Nilai --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="qr-form-label">Jenis Setoran <span>*</span></label>
                    <select wire:model="jenis_setoran" required class="qr-form-input">
                        <option value="">— Pilih —</option>
                        <option value="Al-Qur'an">📖 Al-Qur'an</option>
                        <option value="Jilid/Iqro/Ummi">📗 Jilid / Iqro / Ummi</option>
                        <option value="Hafalan">⭐ Hafalan</option>
                    </select>
                </div>
                <div>
                    <label class="qr-form-label">Predikat Nilai <span>*</span></label>
                    <div class="flex gap-1.5">
                        @foreach([
                            'A' => ['Sangat Lancar', 'A'],
                            'B' => ['Lancar', 'B'],
                            'C' => ['Mengulang', 'C'],
                        ] as $k => $v)
                            <button type="button"
                                    @click="$wire.set('predikat_nilai','{{ $k }}')"
                                    class="qr-grade-btn"
                                    :class="$wire.predikat_nilai === '{{ $k }}' ? 'active-{{ $k }}' : ''">
                                <span>{{ $k }}</span>
                                <span class="qr-grade-sub">{{ $v[0] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ROW 2: Materi & Halaman --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="qr-form-label">Surah / Jilid <span>*</span></label>
                    <input type="text" wire:model="nama_materi" required
                           placeholder="Al-Baqarah / Jilid 4"
                           class="qr-form-input">
                </div>
                <div>
                    <label class="qr-form-label">Ayat / Halaman</label>
                    <input type="text" wire:model="ayat_halaman"
                           placeholder="Misal: 1–10"
                           class="qr-form-input">
                </div>
            </div>

            {{-- ROW 3: Catatan --}}
            <div>
                <label class="qr-form-label">Catatan Guru</label>
                <textarea wire:model="catatan_guru" rows="2"
                          placeholder="Catatan khusus (opsional)…"
                          class="qr-form-input resize-none leading-relaxed"></textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-2.5 pt-1">
                <button type="button" @click="$wire.showFormModal = false"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition-all">
                    Batal
                </button>
                <button type="submit" class="qr-submit-btn flex-1">
                    ✅ &nbsp;Simpan Setoran
                </button>
            </div>
        </form>
    </div>
</div>

{{-- === MANUAL INPUT MODAL === --}}
<x-filament::modal id="manual-input-modal" width="md">
    <x-slot name="heading">Input NIS Manual</x-slot>

    <div class="py-2 text-center" x-data="{ manualNis: '' }">
        <div class="w-10 h-10 mx-auto mb-2 rounded-xl flex items-center justify-center"
             style="background: linear-gradient(135deg,#1e3a8a,#2563eb);">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
        </div>
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Nomor Induk Siswa (NIS)</p>
        <input type="text"
               x-model="manualNis"
               placeholder="Ketik atau scan NIS di sini…"
               autofocus
               @keydown.enter="
                   const v = manualNis.trim();
                   if (v) {
                       window.dispatchEvent(new CustomEvent('manual-nis-submit', { detail: { nis: v } }));
                       manualNis = '';
                       $dispatch('close-modal', { id: 'manual-input-modal' });
                   }
               "
               class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl text-center text-xl font-black tracking-widest focus:border-blue-500 focus:ring-0 outline-none transition-all bg-gray-50 focus:bg-white">
        <p class="text-[10px] text-gray-400 mt-3 font-semibold">
            💡 Arahkan scanner fisik ke kolom ini — NIS akan terisi otomatis.
        </p>

        <div class="flex gap-3 mt-5">
            <button type="button"
                    @click="$dispatch('close-modal', { id: 'manual-input-modal' })"
                    class="qr-btn qr-btn-outline flex-1">Batal</button>
            <button type="button"
                    @click="
                        const v = manualNis.trim();
                        if (v) {
                            window.dispatchEvent(new CustomEvent('manual-nis-submit', { detail: { nis: v } }));
                            manualNis = '';
                            $dispatch('close-modal', { id: 'manual-input-modal' });
                        }
                    "
                    class="qr-btn qr-btn-primary flex-1">✅ Proses NIS</button>
        </div>
    </div>
</x-filament::modal>

{{-- === AUDIO EFFECTS === --}}
<audio id="beep-success" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
</audio>
<audio id="beep-error" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2863/2863-preview.mp3" type="audio/mpeg">
</audio>
<audio id="beep-saved" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2018/2018-preview.mp3" type="audio/mpeg">
</audio>
