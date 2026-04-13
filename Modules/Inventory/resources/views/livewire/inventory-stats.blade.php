<div>
    <div class="grid grid-cols-1 gap-4 mb-6">

        {{-- Total Barang Aktif --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Barang</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $totalBarang }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Katalog Aktif</p>
                </div>
            </div>
        </div>

        {{-- Stok Kritis --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 {{ $stokKritis > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stok Kritis</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums {{ $stokKritis > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">{{ $stokKritis }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Perlu Pengadaan</p>
                </div>
            </div>
        </div>

        {{-- Permintaan Aktif --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 text-warning-600 dark:text-warning-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Permintaan</p>
                    <p class="mt-1 text-2xl font-bold text-warning-600 dark:text-warning-400 tabular-nums">{{ $permintaanOpen }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Menunggu Proses</p>
                </div>
            </div>
        </div>

        {{-- Peminjaman Terlambat --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 {{ $terlambat > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Terlambat</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums {{ $terlambat > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">{{ $terlambat }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Batas Kembali</p>
                </div>
            </div>
        </div>

        {{-- Mutasi Hari Ini --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 p-4 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:ring-gray-950/10 dark:hover:ring-white/20">
            <div class="flex items-center gap-x-4">
                <div class="shrink-0 p-2">
                    <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Mutasi</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $mutasiHariIni }}</p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500 truncate">Transaksi Hari Ini</p>
                </div>
            </div>
        </div>

    </div>

    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $canManage = $user?->hasAnyRole(['super_admin', 'admin_unit']);
    @endphp

        <!-- Actions have been moved to standard Filament header actions in ListBarangs -->

</div>
