<div>
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">

        {{-- Sesi Aktif --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="rounded-lg p-3 shrink-0 ring-1 {{ $sesiAktif > 0 ? 'bg-success-50 dark:bg-success-400/10 ring-success-100 dark:ring-success-400/20' : 'bg-gray-100 dark:bg-gray-800 ring-gray-200 dark:ring-white/5' }}">
                    <svg class="h-6 w-6 {{ $sesiAktif > 0 ? 'text-success-600 dark:text-success-400' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sesi Penilaian</p>
                    <p class="mt-1 text-xl font-bold {{ $sesiAktif > 0 ? 'text-success-600 dark:text-success-400' : 'text-gray-400 dark:text-gray-500' }}">
                        {{ $sesiAktif > 0 ? 'Berjalan' : 'Tidak Ada' }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">{{ $namaSesiAktif ?? 'Belum ada jadwal' }}</p>
                </div>
            </div>
        </div>

        {{-- Total Sesi --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="rounded-lg bg-primary-50 dark:bg-primary-400/10 p-3 shrink-0 ring-1 ring-primary-100 dark:ring-primary-400/20">
                    <svg class="h-6 w-6 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Sesi</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $totalSesi }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Sesi Terdaftar</p>
                </div>
            </div>
        </div>

        {{-- Tugas Pending --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="rounded-lg p-3 shrink-0 ring-1 {{ $tugasPending > 0 ? 'bg-warning-50 dark:bg-warning-400/10 ring-warning-100 dark:ring-warning-400/20' : 'bg-success-50 dark:bg-success-400/10 ring-success-100 dark:ring-success-400/20' }}">
                    <svg class="h-6 w-6 {{ $tugasPending > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Belum Dinilai</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums {{ $tugasPending > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400' }}">{{ $tugasPending }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Butuh Tindakan</p>
                </div>
            </div>
        </div>

        {{-- Tugas Selesai --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="rounded-lg bg-success-50 dark:bg-success-400/10 p-3 shrink-0 ring-1 ring-success-100 dark:ring-success-400/20">
                    <svg class="h-6 w-6 text-success-600 dark:text-success-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Selesai</p>
                    <p class="mt-1 text-2xl font-bold text-success-600 dark:text-success-400 tabular-nums">{{ $tugasSelesai }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Tugas Terisi</p>
                </div>
            </div>
        </div>

        {{-- Tugas Expired --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20 col-span-2 md:col-span-1">
            <div class="flex items-center gap-x-4">
                <div class="rounded-lg p-3 shrink-0 ring-1 {{ $tugasExpired > 0 ? 'bg-danger-50 dark:bg-danger-400/10 ring-danger-100 dark:ring-danger-400/20' : 'bg-gray-100 dark:bg-gray-800 ring-gray-200 dark:ring-white/5' }}">
                    <svg class="h-6 w-6 {{ $tugasExpired > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kadaluarsa</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums {{ $tugasExpired > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $tugasExpired }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Melewati Batas</p>
                </div>
            </div>
        </div>

    </div>


</div>

