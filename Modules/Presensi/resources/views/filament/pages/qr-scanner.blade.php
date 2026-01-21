<x-filament-panels::page>
    @include('presensi::filament.pages.qr-scanner-partials.styles')

    <div x-data="qrScannerData()" :class="{ 'qr-fullscreen': isFullscreen }">

        @include('presensi::filament.pages.qr-scanner-partials.header')

        <div class="qr-container">
            <div class="qr-grid">

                {{-- Left Column: Camera, Mode, Admin --}}
                <div>
                    @include('presensi::filament.pages.qr-scanner-partials.scan-mode')
                    @include('presensi::filament.pages.qr-scanner-partials.admin-panel')
                    @include('presensi::filament.pages.qr-scanner-partials.camera-section')
                </div>

                {{-- Right Column: Stats & History --}}
                @include('presensi::filament.pages.qr-scanner-partials.stats-sidebar')

            </div>
        </div>

        @include('presensi::filament.pages.qr-scanner-partials.modals')
    </div>

    @include('presensi::filament.pages.qr-scanner-partials.scripts')
</x-filament-panels::page>
