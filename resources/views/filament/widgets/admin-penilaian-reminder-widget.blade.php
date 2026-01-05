@php
$count = $this->getPendingCount();
@endphp

@if($count > 0)
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-4 py-3">
            <div class="flex-shrink-0">
                <div class="p-3 bg-warning-500 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <div class="flex-1">
                <h3 class="text-lg font-bold text-warning-600 dark:text-warning-400">
                    ⚠️ Tagihan Penilaian Kinerja!
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Anda memiliki <span class="font-extrabold text-warning-600 dark:text-warning-400">{{ $count }} rekan/staff</span> yang belum dinilai untuk periode <strong>{{ now()->translatedFormat('F Y') }}</strong>.
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                    Silakan selesaikan penilaian sebelum akhir bulan.
                </p>
            </div>

            <div class="flex-shrink-0">
                <x-filament::button
                    color="warning"
                    icon="heroicon-m-pencil-square"
                    tag="a"
                    href="{{ $this->getAssessmentUrl() }}"
                    size="lg">
                    Beri Nilai Sekarang
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
@endif