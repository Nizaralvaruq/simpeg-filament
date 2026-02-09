<div class="md:hidden fixed bottom-0 left-0 right-0 z-50 px-4 py-2 mb-4 mx-4 rounded-2xl shadow-lg border-t-0"
    style="background: rgba(255, 255, 255, 0.4); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.2);">
    <div class="flex justify-around items-center">
        @php
            $navItems = [
                [
                    'url' => \App\Filament\Pages\Dashboard::getUrl(),
                    'icon' => 'heroicon-o-home',
                    'label' => 'Beranda',
                ],
                [
                    'url' => \Modules\Presensi\Filament\Pages\MyAttendance::getUrl(),
                    'icon' => 'heroicon-o-calendar-days',
                    'label' => 'Presensi',
                ],
                [
                    'url' => \Modules\PenilaianKinerja\Filament\Resources\TugasPenilaianSayaResource::getUrl(),
                    'icon' => 'heroicon-o-pencil-square',
                    'label' => 'Penilaian',
                ],
                [
                    'url' => \App\Filament\Pages\EditProfile::getUrl(),
                    'icon' => 'heroicon-o-user',
                    'label' => 'Profil',
                ],
            ];
            $currentUrl = request()->url();
        @endphp

        @foreach ($navItems as $item)
            @php $isActive = $currentUrl === $item['url']; @endphp
            <a href="{{ $item['url'] }}"
                class="flex flex-col items-center transition-all duration-300 {{ $isActive ? 'text-sky-500 scale-110' : 'text-gray-500 hover:text-sky-400' }}">
                <div class="relative p-1">
                    <x-filament::icon alias="panels::widgets.account.logout-button" icon="{{ $item['icon'] }}"
                        class="h-6 w-6 {{ $isActive ? 'drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : '' }}" />
                    @if ($isActive)
                        <span
                            class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-sky-500 rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] font-medium mt-0.5">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<style>
    /* Dark mode support for bottom navigation */
    .dark .md\:hidden.fixed.bottom-0 {
        background: rgba(17, 24, 39, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* Ensure content doesn't get hidden behind bottom nav on mobile */
    @media (max-width: 768px) {
        body {
            padding-bottom: 5rem;
        }
    }

    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>
