@php
$count = $this->getPendingCount();
@endphp

@if($count > 0)
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-4 py-2">
            <div class="p-3 bg-danger-500 rounded-full text-white animate-pulse">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8" />
            </div>

            <div class="flex-1">
                <h3 class="text-lg font-bold text-danger-600 dark:text-danger-400">
                    Tagihan Penilaian Kinerja!
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Anda memiliki <span class="font-extrabold text-danger-600">{{ $count }} rekan</span> yang belum dinilai untuk periode <strong>{{ now()->translatedFormat('F Y') }}</strong>.
                </p>
                <div class="mt-3">
                    <x-filament::button
                        color="danger"
                        icon="heroicon-m-pencil-square"
                        tag="a"
                        href="{{ $this->getUrl() }}">
                        Klik di sini untuk menilai sekarang
                    </x-filament::button>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
@endif