<x-filament-panels::page>
    <div x-data="{ tab: 'personal' }" class="mx-auto w-full max-w-5xl px-4">

        <div class="grid gap-6">

            {{-- HEADER PROFILE --}}
            <x-filament::section>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    {{-- Left --}}
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="h-14 w-14 shrink-0 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-lg font-bold text-gray-700 dark:text-gray-200">
                            {{ strtoupper(mb_substr($record->nama ?? 'P', 0, 1)) }}
                        </div>

                        <div class="min-w-0">
                            <div class="text-xl font-semibold truncate">
                                {{ $record->nama ?? '-' }}
                            </div>

                            {{-- meta inline (ini yang bikin rapi) --}}
                            <div class="mt-2 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-identification" class="h-4 w-4" />
                                    <span class="font-medium">{{ $record->nip ?? '-' }}</span>
                                </span>

                                <span class="inline-flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-briefcase" class="h-4 w-4" />
                                    <span class="font-medium">{{ $record->jabatan ?? '-' }}</span>
                                </span>

                                <span class="inline-flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-building-office-2" class="h-4 w-4" />
                                    <span class="font-medium">{{ $record->units?->pluck('name')->join(', ') ?? '-' }}</span>
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <x-filament::badge color="warning">
                                    {{ $record->status_kepegawaian ?? 'Status -' }}
                                </x-filament::badge>

                                <x-filament::badge color="gray">
                                    Gol: {{ optional($record->golongan)->name ?? '-' }}
                                </x-filament::badge>
                            </div>
                        </div>
                    </div>

                    {{-- Right --}}
                    <div class="sm:pt-1">
                        <x-filament::button
                            icon="heroicon-o-pencil-square"
                            color="warning"
                            tag="a"
                            href="{{ \Modules\Kepegawaian\Filament\Resources\DataIndukResource::getUrl('edit', ['record' => $record]) }}"
                        >
                            Edit
                        </x-filament::button>
                    </div>

                </div>
            </x-filament::section>

            {{-- TABS (pill + scroll) --}}
            <x-filament::section>
                <div class="-mx-2 px-2 overflow-x-auto">
                    <div class="flex gap-2 min-w-max">
                        @php
                            $tabs = [
                                'personal' => 'Personal',
                                'keluarga' => 'Keluarga',
                                'pendidikan' => 'Pendidikan',
                                'rekening' => 'Rekening',
                                'profesi' => 'Profesi',
                            ];
                        @endphp

                        @foreach($tabs as $key => $label)
                            <button
                                type="button"
                                class="px-4 py-2 rounded-full text-sm font-medium border whitespace-nowrap transition
                                    border-gray-200 dark:border-gray-800
                                    hover:bg-gray-50 dark:hover:bg-gray-900"
                                :class="tab === '{{ $key }}'
                                    ? 'bg-primary-600 text-white border-transparent'
                                    : 'bg-transparent text-gray-700 dark:text-gray-200'"
                                @click="tab='{{ $key }}'"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </x-filament::section>

            {{-- helper: row style --}}
            @php
                $row = 'py-3 flex gap-4';
                $dt  = 'w-44 sm:w-52 shrink-0 text-gray-500 dark:text-gray-400';
                $dd  = 'flex-1 font-medium text-gray-100';
            @endphp

            {{-- TAB: PERSONAL --}}
            <x-filament::section x-show="tab==='personal'" x-cloak heading="Informasi Dasar">
                <div class="grid gap-6 md:grid-cols-2">

                    <dl class="divide-y divide-gray-100/10 dark:divide-gray-800">
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">Nama Lengkap</dt>
                            <dd class="{{ $dd }}">{{ $record->nama ?? '-' }}</dd>
                        </div>
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">NIP/NIY</dt>
                            <dd class="{{ $dd }}">{{ $record->nip ?? '-' }}</dd>
                        </div>
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">NIK</dt>
                            <dd class="{{ $dd }}">{{ $record->nik ?? '-' }}</dd>
                        </div>
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">Tempat, Tgl Lahir</dt>
                            <dd class="{{ $dd }}">
                                {{ $record->tempat_lahir ?? '-' }}{{ $record->tempat_lahir && $record->tanggal_lahir ? ', ' : '' }}
                                {{ optional($record->tanggal_lahir)?->translatedFormat('d F Y') ?? ($record->tempat_lahir ? '' : '-') }}
                            </dd>
                        </div>
                    </dl>

                    <dl class="divide-y divide-gray-100/10 dark:divide-gray-800">
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">No HP</dt>
                            <dd class="{{ $dd }}">{{ $record->no_hp ?? '-' }}</dd>
                        </div>
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">Mulai Bertugas</dt>
                            <dd class="{{ $dd }}">{{ optional($record->tmt_awal)?->translatedFormat('d F Y') ?? '-' }}</dd>
                        </div>
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">Unit Kerja</dt>
                            <dd class="{{ $dd }}">{{ $record->units?->pluck('name')->join(', ') ?? '-' }}</dd>
                        </div>
                        <div class="{{ $row }}">
                            <dt class="{{ $dt }}">Alamat</dt>
                            <dd class="{{ $dd }} whitespace-pre-line">{{ $record->alamat ?? '-' }}</dd>
                        </div>
                    </dl>

                </div>
            </x-filament::section>

            {{-- TAB: KELUARGA --}}
            <x-filament::section x-show="tab==='keluarga'" x-cloak heading="Keluarga">
                <dl class="divide-y divide-gray-100/10 dark:divide-gray-800">
                    <div class="{{ $row }}">
                        <dt class="w-56 shrink-0 text-gray-500 dark:text-gray-400">Status Perkawinan</dt>
                        <dd class="{{ $dd }}">{{ $record->status_perkawinan ?? '-' }}</dd>
                    </div>
                    <div class="{{ $row }}">
                        <dt class="w-56 shrink-0 text-gray-500 dark:text-gray-400">Suami/Istri</dt>
                        <dd class="{{ $dd }}">{{ $record->suami_istri ?? '-' }}</dd>
                    </div>
                </dl>
            </x-filament::section>

            {{-- TAB: REKENING/BPJS --}}
            <x-filament::section x-show="tab==='rekening'" x-cloak heading="BPJS / Rekening">
                <dl class="divide-y divide-gray-100/10 dark:divide-gray-800">
                    <div class="{{ $row }}">
                        <dt class="w-56 shrink-0 text-gray-500 dark:text-gray-400">Nomor BPJS</dt>
                        <dd class="{{ $dd }}">{{ $record->no_bpjs ?? '-' }}</dd>
                    </div>
                    <div class="{{ $row }}">
                        <dt class="w-56 shrink-0 text-gray-500 dark:text-gray-400">Nomor KJP 2P</dt>
                        <dd class="{{ $dd }}">{{ $record->no_kjp_2p ?? '-' }}</dd>
                    </div>
                    <div class="{{ $row }}">
                        <dt class="w-56 shrink-0 text-gray-500 dark:text-gray-400">Nomor KJP 3P</dt>
                        <dd class="{{ $dd }}">{{ $record->no_kjp_3p ?? '-' }}</dd>
                    </div>
                </dl>
            </x-filament::section>

            {{-- TAB: PROFESI --}}
            <x-filament::section x-show="tab==='profesi'" x-cloak heading="Riwayat Kepegawaian">
                <div class="grid gap-6 md:grid-cols-2">

                    <div>
                        <div class="mb-2 text-sm font-semibold">Riwayat Jabatan</div>
                        @if($record->riwayatJabatans && $record->riwayatJabatans->count() > 0)
                            <div class="divide-y divide-gray-100/10 dark:divide-gray-800">
                                @foreach($record->riwayatJabatans->sortByDesc('tanggal') as $r)
                                    <div class="py-3 flex gap-4 text-sm">
                                        <div class="w-28 shrink-0 text-gray-500 dark:text-gray-400">
                                            {{ optional($r->tanggal)?->translatedFormat('d M Y') ?? '-' }}
                                        </div>
                                        <div class="flex-1 font-medium">{{ $r->nama_jabatan ?? '-' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $record->jabatan === 'Tetap pada amanahnya' ? 'Tetap pada amanahnya' : 'Belum ada riwayat jabatan' }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="mb-2 text-sm font-semibold">Riwayat Golongan</div>
                        @if($record->riwayatGolongans && $record->riwayatGolongans->count() > 0)
                            <div class="divide-y divide-gray-100/10 dark:divide-gray-800">
                                @foreach($record->riwayatGolongans->sortByDesc('tanggal') as $r)
                                    <div class="py-3 flex gap-4 text-sm">
                                        <div class="w-28 shrink-0 text-gray-500 dark:text-gray-400">
                                            {{ optional($r->tanggal)?->translatedFormat('d M Y') ?? '-' }}
                                        </div>
                                        <div class="flex-1 font-medium">{{ optional($r->golongan)->name ?? '-' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat golongan</div>
                        @endif
                    </div>

                </div>
            </x-filament::section>

            {{-- TAB: PENDIDIKAN --}}
            <x-filament::section x-show="tab==='pendidikan'" x-cloak heading="Pendidikan">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Belum ada data pendidikan (nanti tinggal sambungkan ke relasi jika sudah ada).
                </div>
            </x-filament::section>

        </div>
    </div>
</x-filament-panels::page>
