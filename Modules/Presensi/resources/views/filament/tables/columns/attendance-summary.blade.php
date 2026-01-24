@php
    $record = $getRecord();
    $livewire = $getLivewire();
    $filters = $livewire->getTableFilterState('period');
    $month = $filters['month'] ?? now()->month;
    $year = $filters['year'] ?? now()->year;

    $stats = \Modules\Presensi\Models\Absensi::where('user_id', $record->user_id)
        ->whereMonth('tanggal', $month)
        ->whereYear('tanggal', $year)
        ->get();

    $hadir = $stats->where('status', 'hadir')->count();
    $sakit = $stats->where('status', 'sakit')->count();
    $izin = $stats->where('status', 'izin')->count();
    $alpha = $stats->where('status', 'alpha')->count();
@endphp

<div class="flex items-center gap-2 py-1">
    <div
        class="flex flex-col items-center px-2 py-1 bg-success-50 dark:bg-success-500/10 rounded-lg border border-success-100 dark:border-success-500/20">
        <span class="text-[10px] font-bold uppercase text-success-600 dark:text-success-400">Hadir</span>
        <span class="text-sm font-black text-success-700 dark:text-success-300">{{ $hadir }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-warning-50 dark:bg-warning-500/10 rounded-lg border border-warning-100 dark:border-warning-500/20">
        <span class="text-[10px] font-bold uppercase text-warning-600 dark:text-warning-400">Izin</span>
        <span class="text-sm font-black text-warning-700 dark:text-warning-300">{{ $izin }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-danger-50 dark:bg-danger-500/10 rounded-lg border border-danger-100 dark:border-danger-500/20">
        <span class="text-[10px] font-bold uppercase text-danger-600 dark:text-danger-400">Sakit</span>
        <span class="text-sm font-black text-danger-700 dark:text-danger-300">{{ $sakit }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-gray-50 dark:bg-gray-500/10 rounded-lg border border-gray-100 dark:border-gray-500/20">
        <span class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400">Alpha</span>
        <span class="text-sm font-black text-gray-700 dark:text-gray-300">{{ $alpha }}</span>
    </div>
</div>
