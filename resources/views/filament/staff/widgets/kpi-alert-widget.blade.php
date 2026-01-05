@php
$count = $this->getPendingCount();
$panelId = \Filament\Facades\Filament::getCurrentPanel()?->getId() ?? 'staff';
$url = "/{$panelId}/performance-scores";
@endphp

@if($count > 0)
<x-filament-widgets::widget>
    <x-filament::section
        :heading="'⚠️ Tagihan Penilaian Kinerja'"
        :description="'Periode ' . now()->translatedFormat('F Y')">
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="w-12 h-12 bg-warning-100 dark:bg-warning-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        Anda memiliki <span class="text-warning-600 dark:text-warning-400 font-bold">{{ $count }} rekan</span> yang belum dinilai
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Silakan selesaikan penilaian sebelum akhir bulan
                    </p>

                    <div class="mt-3">
                        <x-filament::button
                            color="warning"
                            icon="heroicon-m-pencil-square"
                            tag="a"
                            :href="$url"
                            size="sm">
                            Beri Nilai Sekarang
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
@endif