<x-filament-panels::page>
    @if($this->data)
    {{ $this->infolist }}
    @else
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-medium text-gray-900">Data Profil Belum Tersedia</h2>
        <p class="mt-1 text-gray-600">Hubungi Admin HR untuk menghubungkan akun Anda dengan Data Pegawai.</p>
    </div>
    @endif
</x-filament-panels::page>