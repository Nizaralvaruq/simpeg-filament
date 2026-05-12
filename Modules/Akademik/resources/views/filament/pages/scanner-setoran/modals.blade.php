{{-- === MODAL KONFIRMASI BUAT SISWA BARU === --}}
<div x-show="$wire.showConfirmNewSiswa" x-cloak
     class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center z-[2100] p-4"
     x-on:keydown.escape="$wire.cancelCreateSiswa()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700"
         style="width: 100%; max-width: 400px;">
        <div class="p-5">
            {{-- Row: icon + judul + tombol X --}}
            <div class="flex items-start gap-3 mb-3">
                <div class="rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0"
                     style="width: 36px; height: 36px; margin-top: 2px;">
                    <svg style="width:20px;height:20px;" class="text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-black text-gray-800 dark:text-gray-100">NIS Tidak Ditemukan</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        NIS <code class="px-1 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 rounded font-mono font-bold" x-text="$wire.pendingNewNis"></code>
                        belum terdaftar. Buat data baru?
                    </p>
                </div>
                <button wire:click="cancelCreateSiswa"
                        class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div style="padding: 0 20px 16px 48px;">
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="pendingNamaSiswa" placeholder="Siswa Baru" required
                           class="w-full text-sm font-semibold"
                           style="display: block; width: 100%; padding: 8px 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; color: #1f2937; outline: none;">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">WA Orang Tua <span class="text-xs text-gray-400 font-normal normal-case">(Opsional)</span></label>
                    <input type="text" wire:model="pendingWaOrtu" placeholder="0812..."
                           class="w-full text-sm font-semibold"
                           style="display: block; width: 100%; padding: 8px 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; color: #1f2937; outline: none;">
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex gap-2" style="padding-left:48px;">
                <button type="button" wire:click="cancelCreateSiswa" wire:loading.attr="disabled"
                        class="flex-1 rounded-xl font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        style="padding: 8px 0; font-size:12px;">
                    Batal
                </button>
                <button type="button" wire:click="confirmCreateSiswa" wire:loading.attr="disabled"
                        class="flex-1 rounded-xl font-bold text-white bg-amber-500 hover:bg-amber-400 transition-colors shadow-sm"
                        style="padding: 8px 0; font-size:12px;">
                    <span wire:loading.remove wire:target="confirmCreateSiswa">✅ Ya, Buat</span>
                    <span wire:loading wire:target="confirmCreateSiswa">Proses…</span>
                </button>
            </div>
        </div>
    </div>
</div>


{{-- === SETORAN INPUT MODAL === --}}

<div x-show="$wire.showFormModal" x-cloak class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center z-[2000] p-4 transition-opacity duration-200" @keydown.escape.window="$wire.showFormModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl border border-gray-100 dark:border-gray-700 transform transition-all" @click.away="$wire.showFormModal = false">

        {{-- MODAL HEADER --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-500 p-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-white/20 border-2 border-white/30 flex items-center justify-center text-xl font-black text-white shrink-0 overflow-hidden">
                <img :src="$wire.scannedUser?.avatar" class="w-full h-full object-cover"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span style="display:none; align-items:center; justify-content:center; width:100%; height:100%;"
                      x-text="$wire.scannedUser?.name?.charAt(0)?.toUpperCase() ?? '?'"></span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 bg-white/20 text-white text-[10px] font-black rounded-md tracking-widest border border-white/20 shadow-sm">SISWA</span>
                    <h2 class="text-lg font-bold text-white truncate" x-text="$wire.scannedUser?.name"></h2>
                </div>
                <p class="text-xs text-emerald-100 font-medium">
                    NIS: <span class="font-mono font-bold text-white tracking-wider" x-text="$wire.scannedUser?.nis"></span>
                    &nbsp;·&nbsp; Kelas <span x-text="$wire.scannedUser?.kelas"></span>
                </p>
            </div>
            <button @click="$wire.showFormModal = false" class="ml-auto p-2 rounded-lg bg-white/10 text-white hover:bg-rose-500 hover:text-white transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- BANNER SISWA BARU (otomatis dibuat) --}}
        <div x-show="$wire.scannedUser?.is_new" x-cloak
             class="px-5 py-3 bg-amber-50 border-b border-amber-300 flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p class="text-xs font-black text-amber-800 uppercase tracking-wide">Siswa Baru Dibuat Otomatis</p>
                <p class="text-[11px] text-amber-700 mt-0.5">
                    NIS ini belum ada di database. Data siswa dibuat sementara — silakan lengkapi namanya melalui menu
                    <strong>Data QR Siswa</strong> setelah setoran disimpan.
                </p>
            </div>
        </div>

        {{-- RIWAYAT TERAKHIR --}}
        <div x-show="$wire.riwayatTerakhir" x-cloak class="px-5 py-3 bg-amber-50 border-b border-amber-200 flex items-center gap-3">
            <span class="px-2 py-0.5 rounded border border-amber-300 bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest shrink-0">Terakhir</span>
            <span class="text-amber-900 font-bold text-xs truncate"
                  x-text="$wire.riwayatTerakhir?.tanggal + ' — ' + $wire.riwayatTerakhir?.materi"></span>
        </div>

        {{-- FORM --}}
        <form wire:submit.prevent="saveSetoran" class="p-6 space-y-5">

            {{-- ROW 1: Jenis & Nilai --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Jenis Setoran <span class="text-rose-500">*</span></label>
                    <select wire:model="jenis_setoran" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 block p-3 transition-shadow">
                        <option value="">— Pilih —</option>
                        <option value="Al-Qur'an">📖 Al-Qur'an</option>
                        <option value="Jilid/Iqro/Ummi">📗 Jilid / Iqro / Ummi</option>
                        <option value="Hafalan">⭐ Hafalan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Predikat Nilai <span class="text-rose-500">*</span></label>
                    <div class="flex gap-2">
                        @foreach([
                            'A' => ['Sangat Lancar', 'emerald'],
                            'B' => ['Lancar', 'blue'],
                            'C' => ['Mengulang', 'rose'],
                        ] as $k => $v)
                            <button type="button"
                                    @click="$wire.set('predikat_nilai','{{ $k }}')"
                                    class="flex-1 p-2 rounded-xl border-2 transition-all flex flex-col items-center justify-center gap-0.5 font-black text-sm"
                                    :class="$wire.predikat_nilai === '{{ $k }}' 
                                        ? 'bg-{{ $v[1] }}-100 text-{{ $v[1] }}-700 border-{{ $v[1] }}-500 scale-105 shadow-sm' 
                                        : 'bg-gray-50 text-gray-400 border-gray-200 hover:border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500 dark:hover:border-gray-500'">
                                <span>{{ $k }}</span>
                                <span class="text-[9px] uppercase tracking-wider opacity-80">{{ $v[0] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ROW 2: Materi & Halaman --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Surah / Jilid <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="nama_materi" required
                           placeholder="Al-Baqarah / Jilid 4"
                           class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 block p-3 transition-shadow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Ayat / Halaman</label>
                    <input type="text" wire:model="ayat_halaman"
                           placeholder="Misal: 1–10"
                           class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 text-sm font-semibold rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 block p-3 transition-shadow">
                </div>
            </div>

            {{-- ROW 3: Catatan --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Catatan Guru</label>
                <textarea wire:model="catatan_guru" rows="2"
                          placeholder="Catatan khusus (opsional)…"
                          class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 text-sm font-medium rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 block p-3 transition-shadow resize-none"></textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-3 pt-3">
                <button type="button" @click="$wire.showFormModal = false"
                        class="px-5 py-3 rounded-xl text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-5 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-emerald-500/30 transform hover:-translate-y-0.5">
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
        <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-500 shadow-sm">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
        </div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Nomor Induk Siswa (NIS)</p>
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
               class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-2xl text-center text-xl font-black tracking-widest focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 outline-none transition-all bg-gray-50 dark:bg-gray-900 focus:bg-white dark:focus:bg-gray-800 text-gray-900 dark:text-white shadow-inner">
        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-4 font-semibold">
            💡 Arahkan scanner fisik ke kolom ini — NIS akan terisi otomatis.
        </p>

        <div class="flex gap-3 mt-6">
            <button type="button"
                    @click="$dispatch('close-modal', { id: 'manual-input-modal' })"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors shadow-sm">Batal</button>
            <button type="button"
                    @click="
                        const v = manualNis.trim();
                        if (v) {
                            window.dispatchEvent(new CustomEvent('manual-nis-submit', { detail: { nis: v } }));
                            manualNis = '';
                            $dispatch('close-modal', { id: 'manual-input-modal' });
                        }
                    "
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md hover:shadow-lg transition-all">✅ Proses NIS</button>
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
