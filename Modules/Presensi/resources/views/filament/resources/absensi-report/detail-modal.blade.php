<div class="space-y-4">
    <div
        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode Laporan
            </h3>
            <p class="text-lg font-bold text-gray-900 dark:text-white">
                {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="flex gap-4">
            <div class="text-center">
                <span class="block text-xs text-gray-400">Hadir</span>
                <span
                    class="text-lg font-black text-success-600">{{ $absensis->where('status', 'hadir')->count() }}</span>
            </div>
            <div class="text-center">
                <span class="block text-xs text-gray-400">Lainnya</span>
                <span
                    class="text-lg font-black text-warning-600">{{ $absensis->whereIn('status', ['izin', 'sakit', 'alpha'])->count() }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-hidden border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm">
        <table class="w-full text-left divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Masuk</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Pulang</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Terlambat</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($absensis as $absensi)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium">
                            <span
                                class="text-gray-900 dark:text-white">{{ $absensi->tanggal->translatedFormat('d M Y') }}</span>
                            <span
                                class="block text-[10px] text-gray-400">{{ $absensi->tanggal->translatedFormat('l') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $color = match ($absensi->status) {
                                    'hadir' => 'text-success-600 bg-success-50 dark:bg-success-500/10',
                                    'izin' => 'text-warning-600 bg-warning-50 dark:bg-warning-500/10',
                                    'sakit' => 'text-danger-600 bg-danger-50 dark:bg-danger-500/10',
                                    'alpha' => 'text-gray-600 bg-gray-50 dark:bg-gray-500/10',
                                    default => 'text-gray-400',
                                };
                            @endphp
                            <span class="px-2 py-0.5 text-[11px] font-bold uppercase rounded-full {{ $color }}">
                                {{ $absensi->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center tabular-nums">
                            {{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '--:--' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center tabular-nums">
                            {{ $absensi->jam_keluar ? \Carbon\Carbon::parse($absensi->jam_keluar)->format('H:i') : '--:--' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-center tabular-nums">
                            @if ($absensi->late_minutes > 0)
                                <span class="text-danger-600 font-bold">{{ $absensi->late_minutes }} mnt</span>
                            @else
                                <span class="text-success-600">0 mnt</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">
                            Tidak ada data absensi untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
