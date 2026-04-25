<x-filament-panels::page>
    @include('akademik::filament.pages.scanner-setoran.styles')

    <div x-data="qrScannerData()" :class="{ 'qr-fullscreen': isFullscreen }" class="qr-wrap relative min-h-screen">

        @include('akademik::filament.pages.scanner-setoran.header')

        <div class="max-w-7xl mx-auto py-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                {{-- LEFT COLUMN: CAMERA & SCANNER --}}
                <div class="lg:col-span-8 space-y-6">
                    @include('akademik::filament.pages.scanner-setoran.camera-section')
                </div>

                {{-- RIGHT COLUMN: SIDEBAR STATS & HISTORY --}}
                <div class="lg:col-span-4 space-y-6">
                    @include('akademik::filament.pages.scanner-setoran.stats-sidebar')
                </div>
            </div>
        </div>

        @include('akademik::filament.pages.scanner-setoran.modals')
    </div>

    @include('akademik::filament.pages.scanner-setoran.scripts')
</x-filament-panels::page>
