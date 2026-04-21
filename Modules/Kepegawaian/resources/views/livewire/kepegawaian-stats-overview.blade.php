@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    
    // Dasar Query
    $query = \Modules\Kepegawaian\Models\DataInduk::query();
    
    // Filter RBAC (Sama persis dengan filter DataIndukResource)
    if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
        if ($user->hasAnyRole(['kepala_sekolah', 'koor_jenjang', 'admin_unit'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->all();
                $query->whereHas('units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                $query->whereRaw('1=0');
            }
        } elseif ($user->hasRole('staff')) {
            $query->where('id', $user->employee?->id ?? -1);
        } else {
            $query->whereRaw('1=0');
        }
    }

    // Menghitung Metrik
    $totalPegawai = (clone $query)->count();
    $pegawaiAktif = (clone $query)->where('status', 'Aktif')->count();
    $pegawaiIzinCuti = (clone $query)->whereIn('status', ['Cuti', 'Izin', 'Sakit'])->count();
    $pegawaiTetap = (clone $query)->where('status_kepegawaian', 'Tetap')->count();
@endphp

<div>
    {{-- Kita gunakan "grid-cols-1 md:grid-cols-2 lg:grid-cols-4" agar menyamping / horizontal --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Total Pegawai --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Pegawai</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ number_format($totalPegawai, 0, ',', '.') }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Sesuai Akses Anda</p>
                </div>
            </div>
        </div>

        {{-- Pegawai Aktif --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 text-success-600 dark:text-success-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pegawai Aktif</p>
                    <p class="mt-1 text-2xl font-bold text-success-600 dark:text-success-400 tabular-nums">{{ number_format($pegawaiAktif, 0, ',', '.') }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Status = Aktif</p>
                </div>
            </div>
        </div>

        {{-- Pegawai Izin/Cuti --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 text-warning-600 dark:text-warning-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Non-Aktif/Izin</p>
                    <p class="mt-1 text-2xl font-bold text-warning-600 dark:text-warning-400 tabular-nums">{{ number_format($pegawaiIzinCuti, 0, ',', '.') }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Sakit, Izin, Cuti</p>
                </div>
            </div>
        </div>

        {{-- Pegawai Tetap --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pegawai Tetap</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ number_format($pegawaiTetap, 0, ',', '.') }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Status Kebijakan</p>
                </div>
            </div>
        </div>

    </div>
</div>
