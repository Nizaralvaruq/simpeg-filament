@php
    $record = $getRecord();
@endphp

<div class="flex items-center gap-2 py-1">
    <div
        class="flex flex-col items-center px-2 py-1 bg-success-50 dark:bg-success-500/10 rounded-lg border border-success-100 dark:border-success-500/20">
        <span class="text-[10px] font-bold uppercase text-success-600 dark:text-success-400">Hadir</span>
        <span class="text-sm font-black text-success-700 dark:text-success-300">{{ $record->hadir_count ?? 0 }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-info-50 dark:bg-info-500/10 rounded-lg border border-info-100 dark:border-info-500/20">
        <span class="text-[10px] font-bold uppercase text-info-600 dark:text-info-400">DL</span>
        <span class="text-sm font-black text-info-700 dark:text-info-300">{{ $record->dinas_luar_count ?? 0 }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-warning-50 dark:bg-warning-500/10 rounded-lg border border-warning-100 dark:border-warning-500/20">
        <span class="text-[10px] font-bold uppercase text-warning-600 dark:text-warning-400">Izin</span>
        <span class="text-sm font-black text-warning-700 dark:text-warning-300">{{ $record->izin_count ?? 0 }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-danger-50 dark:bg-danger-500/10 rounded-lg border border-danger-100 dark:border-danger-500/20">
        <span class="text-[10px] font-bold uppercase text-danger-600 dark:text-danger-400">Sakit</span>
        <span class="text-sm font-black text-danger-700 dark:text-danger-300">{{ $record->sakit_count ?? 0 }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-gray-50 dark:bg-gray-500/10 rounded-lg border border-gray-100 dark:border-gray-500/20">
        <span class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400">Alpha</span>
        <span class="text-sm font-black text-gray-700 dark:text-gray-300">{{ $record->alpha_count ?? 0 }}</span>
    </div>
    <div
        class="flex flex-col items-center px-2 py-1 bg-primary-50 dark:bg-primary-500/10 rounded-lg border border-primary-100 dark:border-primary-500/20">
        <span class="text-[10px] font-bold uppercase text-primary-600 dark:text-primary-400">Cuti</span>
        <span class="text-sm font-black text-primary-700 dark:text-primary-300">{{ $record->cuti_count ?? 0 }}</span>
    </div>
</div>
