<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $data = $this->getUserData();
        @endphp

        <div class="flex flex-col md:flex-row items-center gap-6">
            @if ($data['has_nip'])
                <!-- QR Code Section -->
                <div
                    class="bg-white p-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 flex-shrink-0">
                    <div class="w-48 h-48">
                        {!! $data['qr_code'] !!}
                    </div>
                    <p class="text-center text-xs font-mono mt-2 text-gray-500 tracking-widest">{{ $data['nip'] }}</p>
                </div>

                <!-- Info Section -->
                <div class="flex-1 text-center md:text-left space-y-2">
                    <div class="inline-block p-1 rounded-full bg-primary-50 dark:bg-primary-900/10 mb-2">
                        <img src="{{ $data['avatar'] }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $data['name'] }}
                    </h2>

                    <div class="flex flex-col md:flex-row gap-2 md:gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <span class="flex items-center gap-1 justify-center md:justify-start">
                            <x-filament::icon icon="heroicon-m-identification" class="w-4 h-4" />
                            {{ $data['nip'] }}
                        </span>
                        <span class="flex items-center gap-1 justify-center md:justify-start">
                            <x-filament::icon icon="heroicon-m-briefcase" class="w-4 h-4" />
                            {{ $data['jabatan'] }}
                        </span>
                    </div>

                    <div class="pt-4">
                        <p class="text-sm text-gray-500">
                            Tunjukkan QR Code ini ke kamera petugas piket untuk melakukan Absensi (Masuk/Pulang).
                        </p>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center w-full py-6 text-center text-gray-500">
                    <x-filament::icon icon="heroicon-o-exclamation-circle" class="w-12 h-12 mb-2 text-warning-500" />
                    <h3 class="font-medium text-lg">Data Kepegawaian Belum Lengkap</h3>
                    <p class="text-sm max-w-md mx-auto mt-1">
                        Akun Anda belum terhubung dengan Data Induk atau belum memiliki NIP. Hubungi Admin untuk
                        melengkapi data.
                    </p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
