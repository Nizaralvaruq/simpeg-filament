<x-filament-panels::page>
    <div>
        <form wire:submit.prevent="submit" class="space-y-6">
            {{ $this->form }}

            {{-- Submit Action --}}
            <div
                class="flex flex-col items-center justify-center gap-4 pt-8 border-t border-gray-100 dark:border-white/5">
                <x-filament::button type="submit" size="xl" color="primary" icon="heroicon-o-paper-airplane"
                    class="w-full sm:w-80 shadow-2xl transition-all active:scale-95 py-4">
                    <span wire:loading.remove>Simpan Kehadiran</span>
                    <span wire:loading>Memproses...</span>
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
