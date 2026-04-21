{{-- === SETORAN INPUT MODAL === --}}
<div x-show="$wire.showFormModal" x-cloak class="qr-modal-overlay" @keydown.escape.window="$wire.showFormModal = false">
    <div class="qr-modal-card" @click.away="$wire.showFormModal = false">

        {{-- HEADER SISWA --}}
        <div class="px-7 py-5 flex items-center gap-4" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
            <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-md flex-shrink-0"
                 style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                <img :src="$wire.scannedUser?.avatar" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="px-2 py-0.5 bg-blue-600 text-white text-[9px] font-black rounded-md tracking-widest">SISWA</span>
                    <h2 class="text-lg font-black text-gray-900 truncate" x-text="$wire.scannedUser?.name"></h2>
                </div>
                <p class="text-xs text-gray-500">
                    NIS: <span class="font-mono font-bold text-blue-600" x-text="$wire.scannedUser?.nis"></span>
                    &nbsp;|&nbsp; Kelas <span x-text="$wire.scannedUser?.kelas"></span>
                </p>
            </div>
            <button @click="$wire.showFormModal = false"
                    class="p-2 rounded-xl text-gray-400 hover:text-rose-500 hover:bg-rose-50 transition-all flex-shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- RIWAYAT TERAKHIR --}}
        <div x-show="$wire.riwayatTerakhir" x-cloak
             class="px-7 py-2.5 flex items-center gap-3 text-xs"
             style="background:#fffbeb; border-top:1px solid #fde68a; border-bottom:1px solid #fde68a;">
            <span class="px-2 py-0.5 bg-amber-400 text-white font-black text-[9px] rounded-md tracking-widest">TERAKHIR</span>
            <span class="text-amber-800 font-bold" x-text="$wire.riwayatTerakhir?.tanggal + ': ' + $wire.riwayatTerakhir?.materi"></span>
        </div>

        {{-- FORM --}}
        <form wire:submit.prevent="saveSetoran" class="p-6 space-y-4">

            {{-- ROW 1: Jenis & Nilai --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[.1em] mb-1.5">
                        Jenis Setoran <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="jenis_setoran" required
                            class="w-full h-10 rounded-xl border border-gray-200 bg-gray-50 text-sm font-semibold px-3 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition-all">
                        <option value="">-- Pilih --</option>
                        <option value="Al-Qur'an">📖 Al-Qur'an</option>
                        <option value="Jilid/Iqro/Ummi">📗 Jilid / Iqro / Ummi</option>
                        <option value="Hafalan">⭐ Hafalan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[.1em] mb-1.5">
                        Predikat Nilai <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex gap-1.5">
                        @foreach(['A' => ['Sangat Lancar','#16a34a','#dcfce7','#bbf7d0'],
                                  'B' => ['Lancar','#2563eb','#dbeafe','#bfdbfe'],
                                  'C' => ['Mengulang','#dc2626','#fee2e2','#fecaca']] as $k => $v)
                            <button type="button"
                                    @click="$wire.set('predikat_nilai','{{ $k }}')"
                                    :title="'{{ $v[0] }}'"
                                    class="flex-1 h-10 rounded-xl text-sm font-black transition-all border-2"
                                    :style="$wire.predikat_nilai === '{{ $k }}'
                                        ? 'background:{{ $v[2] }};color:{{ $v[1] }};border-color:{{ $v[1] }};transform:scale(1.05);'
                                        : 'background:#f9fafb;color:#9ca3af;border-color:#e5e7eb;'">
                                {{ $k }}
                            </button>
                        @endforeach
                    </div>
                    <p class="text-[10px] mt-1 font-bold"
                       :style="$wire.predikat_nilai==='A'?'color:#16a34a;':($wire.predikat_nilai==='B'?'color:#2563eb;':($wire.predikat_nilai==='C'?'color:#dc2626;':'color:#9ca3af;'))"
                       x-text="$wire.predikat_nilai==='A'?'✓ Sangat Lancar':($wire.predikat_nilai==='B'?'✓ Lancar':($wire.predikat_nilai==='C'?'⚠ Kurang Lancar / Mengulang':'Pilih nilai di atas'))">
                    </p>
                </div>
            </div>

            {{-- ROW 2: Materi & Halaman --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[.1em] mb-1.5">
                        Surah / Jilid <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="nama_materi" required placeholder="Al-Baqarah / Jilid 4"
                           class="w-full h-10 rounded-xl border border-gray-200 bg-gray-50 text-sm px-3 font-semibold focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[.1em] mb-1.5">Ayat / Halaman</label>
                    <input type="text" wire:model="ayat_halaman" placeholder="Misal: 1-10"
                           class="w-full h-10 rounded-xl border border-gray-200 bg-gray-50 text-sm px-3 font-semibold focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition-all">
                </div>
            </div>

            {{-- ROW 3: Catatan --}}
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[.1em] mb-1.5">Catatan Guru</label>
                <textarea wire:model="catatan_guru" rows="2" placeholder="Catatan khusus (opsional)..."
                          class="w-full rounded-xl border border-gray-200 bg-gray-50 text-sm px-3 py-2 font-semibold focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition-all resize-none"></textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-3 pt-1">
                <button type="button" @click="$wire.showFormModal = false"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl font-black text-sm text-white transition-all active:scale-[.98] shadow-lg"
                        style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 14px rgba(37,99,235,.4);">
                    ✅ SIMPAN SETORAN
                </button>
            </div>
        </form>
    </div>
</div>

{{-- === MANUAL INPUT MODAL === --}}
<x-filament::modal id="manual-input-modal" width="md">
    <x-slot name="heading">Input NIS Manual</x-slot>

    {{-- x-data lokal agar state manualNis bisa diakses oleh input & tombol dalam satu scope --}}
    <div class="py-6 text-center" x-data="{ manualNis: '' }">
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Nomor Induk Siswa (NIS)</p>
        <input type="text"
               x-model="manualNis"
               placeholder="Ketik atau scan NIS di sini..."
               autofocus
               @keydown.enter="
                   const v = manualNis.trim();
                   if (v) {
                       window.dispatchEvent(new CustomEvent('manual-nis-submit', { detail: { nis: v } }));
                       manualNis = '';
                       $dispatch('close-modal', { id: 'manual-input-modal' });
                   }
               "
               class="w-full px-5 py-4 border-2 border-gray-200 rounded-2xl text-center text-xl font-black tracking-widest focus:border-blue-500 focus:ring-0 outline-none transition-all">
        <p class="text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-wider">
            💡 Arahkan scanner fisik ke sini, NIS akan terisi otomatis.
        </p>

        {{-- Footer di dalam x-data yang sama supaya bisa akses manualNis --}}
        <div class="flex gap-3 mt-6">
            <button type="button"
                    @click="$dispatch('close-modal', { id: 'manual-input-modal' })"
                    class="qr-btn qr-btn-outline flex-1 justify-center">Batal</button>
            <button type="button"
                    @click="
                        const v = manualNis.trim();
                        if (v) {
                            window.dispatchEvent(new CustomEvent('manual-nis-submit', { detail: { nis: v } }));
                            manualNis = '';
                            $dispatch('close-modal', { id: 'manual-input-modal' });
                        }
                    "
                    class="qr-btn qr-btn-primary flex-1 justify-center">✅ Proses NIS</button>
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
