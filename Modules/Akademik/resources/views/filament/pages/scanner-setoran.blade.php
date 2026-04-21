<x-filament-panels::page>
    @include('akademik::filament.pages.scanner-setoran.styles')

    <div x-data="qrScannerData()" :class="{ 'qr-fullscreen': isFullscreen }" class="relative min-h-screen">

        @include('akademik::filament.pages.scanner-setoran.header')

        <div class="qr-container">
            <div class="qr-grid">
                {{-- LEFT COLUMN: CAMERA & SCANNER --}}
                @include('akademik::filament.pages.scanner-setoran.camera-section')

                {{-- RIGHT COLUMN: SIDEBAR STATS & HISTORY --}}
                @include('akademik::filament.pages.scanner-setoran.stats-sidebar')
            </div>
        </div>

        @include('akademik::filament.pages.scanner-setoran.modals')
    </div>

    @include('akademik::filament.pages.scanner-setoran.scripts')
</x-filament-panels::page>
