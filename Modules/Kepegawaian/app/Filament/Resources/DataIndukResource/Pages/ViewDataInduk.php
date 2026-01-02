<?php

namespace Modules\Kepegawaian\Filament\Resources\DataIndukResource\Pages;

use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Kepegawaian\Filament\Resources\DataIndukResource;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

use Filament\Infolists\Components\TextEntry;

class ViewDataInduk extends ViewRecord
{
    protected static string $resource = DataIndukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Edit Data'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(12) 
            ->components([
                Tabs::make('Data Pegawai')
                    ->columnSpanFull() 
                    ->tabs([
                        Tab::make('Informasi Pribadi')
                            ->schema([
                                Section::make()
                                    ->columns(1)
                                    ->schema([
                                        TextEntry::make('nama')->label('Nama Lengkap')->weight('bold')->inlineLabel(),
                                        TextEntry::make('nik')->label('NIK')->inlineLabel(),
                                        TextEntry::make('no_hp')->label('Nomor HP')->inlineLabel(),
                                        TextEntry::make('ttl')
                                            ->label('Tempat, Tanggal Lahir')
                                            ->inlineLabel()
                                            ->state(function ($record) {
                                                $tempat = $record->tempat_lahir;

                                                $tanggal = $record->tanggal_lahir
                                                    ? Carbon::parse($record->tanggal_lahir)->translatedFormat('d F Y')
                                                    : null;

                                                if ($tempat && $tanggal) {
                                                    return "{$tempat}, {$tanggal}";
                                                }

                                                return $tempat ?: ($tanggal ?: '-');
                                            }),
                                        TextEntry::make('pendidikan')->label('Pendidikan')->inlineLabel(),
                                        TextEntry::make('instansi')->label('Instansi')->inlineLabel(),
                                        TextEntry::make('status_perkawinan')->label('Status Perkawinan')->inlineLabel(),
                                        TextEntry::make('suami_istri')->label('Nama Suami / Istri')->inlineLabel(),
                                        TextEntry::make('alamat')->label('Alamat')->inlineLabel()->columnSpanFull(),
                                        TextEntry::make('user.email')->label('Email')->inlineLabel()->placeholder('-'),
                                    ]),
                            ]),

                        Tab::make('Data Induk')
                            ->schema([
                                Section::make()
                                    ->columns(1)
                                    ->schema([
                                        TextEntry::make('nip')->label('NPA')->inlineLabel(),
                                        TextEntry::make('jabatan')->label('Jabatan')->inlineLabel(),
                                        TextEntry::make('tmt_awal')->label('Mulai Bertugas')->date('d F Y')->inlineLabel(),
                                        TextEntry::make('units.name')->label('Unit Kerja')->badge()->separator(', ')->inlineLabel(),
                                        TextEntry::make('status_kepegawaian')->label('Status Kepegawaian')->badge()->inlineLabel(),
                                        TextEntry::make('golongan.name')->label('Golongan')->badge()->inlineLabel(),
                                        TextEntry::make('tanggal_golongan_terbaru')
                                            ->label('Tanggal Golongan')
                                            ->inlineLabel()
                                            ->state(function ($record) {
                                                $latest = $record->riwayatGolongans
                                                    ?->sortBy('tanggal')
                                                    ->first();

                                                return $latest?->tanggal
                                                    ? $latest->tanggal->translatedFormat('d F Y')
                                                    : '-';
                                            }),
                                        TextEntry::make('status')->label('Status')
                                            ->badge()
                                            ->color(fn ($state) => match ($state) {
                                                    'Aktif' => 'success',
                                                    'Cuti' => 'warning',
                                                    'Resign' => 'danger',
                                            })
                                            ->inlineLabel(),
                                        TextEntry::make('keterangan')->label('Keterangan')->inlineLabel(),
                                    ]),
                            ]),

                        Tab::make('BPJS')
                            ->schema([
                                Section::make()
                                    ->columns(columns: 1)
                                    ->schema([
                                        TextEntry::make('no_bpjs')->label('Nomor BPJS')->inlineLabel()->placeholder('Tidak ikut lembaga'),
                                        TextEntry::make('no_kjp_2p')->label('Nomor KJP 2P')->inlineLabel()->placeholder('Tidak ikut lembaga'),
                                        TextEntry::make('no_kjp_3p')->label('Nomor KJP 3P')->inlineLabel()->placeholder('Tidak ikut lembaga'),

                                    ]),
                            ]),

                        Tab::make('Riwayat Kepegawaian')
                            ->schema([
                                Section::make('Riwayat Jabatan')
                                    ->schema([
                                        TextEntry::make('riwayat_jabatan')
                                            ->state(function ($record) {

                                                if ($record->pindah_tugas === 'tetap') {
                                                    return 'Tetap pada amanahnya – ' . ($record->jabatan ?? '-');
                                                }

                                                if ($record->riwayatJabatans && $record->riwayatJabatans->count()) {
                                                    return $record->riwayatJabatans
                                                        ->sortBy('tanggal')
                                                        ->map(fn ($r) =>
                                                            ($r->tanggal?->format('d M Y') ?? '-') .
                                                            ' — ' . ($r->nama_jabatan ?? '-')
                                                        )
                                                        ->implode('<br>');
                                                }

                                                return '-';
                                            })
                                            ->markdown(),
                                    ]),

                                Section::make('Riwayat Golongan')
                                    ->schema([
                                        TextEntry::make('riwayat_golongan')
                                            ->state(fn ($record) =>
                                                $record->riwayatGolongans?->sortBy('tanggal')
                                                    ->map(fn ($r) =>
                                                        ($r->tanggal?->format('d M Y') ?? '-') .
                                                        ' — ' . (optional($r->golongan)->name ?? '-')
                                                    )->implode('<br>')
                                                ?: 'Belum ada riwayat golongan'
                                            )
                                            ->markdown(),
                                    ]),
                            ]),

                        Tab::make('Akun')
                            ->schema([
                                Section::make()
                                    ->columns(columns: 1)
                                    ->schema([
                                        TextEntry::make('user.email')->label('Email login')->inlineLabel(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

}
