<x-filament-widgets::widget>
    @php
    $employee = $this->getEmployee();
    @endphp

    @if($employee)
    <x-filament::section>
        <x-slot name="heading">
            Data Pribadi
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $employee->nama }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">NIP</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $employee->nip ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tempat, Tanggal Lahir</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    {{ $employee->tempat_lahir ?? '-' }}{{ $employee->tanggal_lahir ? ', ' . $employee->tanggal_lahir->format('d F Y') : '' }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No. HP</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $employee->no_hp ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $employee->user->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jabatan</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $employee->jabatan ?? '-' }}</p>
            </div>
        </div>
    </x-filament::section>
    @else
    <x-filament::section>
        <x-slot name="heading">
            Data Pribadi
        </x-slot>

        <p class="text-sm text-gray-500 dark:text-gray-400">Data profil belum tersedia. Hubungi Admin HR.</p>
    </x-filament::section>
    @endif
</x-filament-widgets::widget>