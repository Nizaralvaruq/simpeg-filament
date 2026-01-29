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
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;

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
                                        ImageEntry::make('foto_profil')
                                            ->label('Foto Profil')
                                            ->circular()
                                            ->disk('public')
                                            ->inlineLabel(),

                                        TextEntry::make('nama')->label('Nama Lengkap')->weight('bold')->inlineLabel(),
                                        TextEntry::make('nik')->label('NIK')->inlineLabel(),
                                        TextEntry::make('jenis_kelamin')->label('Jenis Kelamin')->inlineLabel(),
                                        TextEntry::make('agama')->label('Agama')->inlineLabel(),
                                        TextEntry::make('golongan_darah')->label('Golongan Darah')->inlineLabel(),
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

                                        TextEntry::make('no_hp')->label('Nomor HP')->inlineLabel(),
                                        TextEntry::make('status_perkawinan')->label('Status Perkawinan')->inlineLabel(),
                                        TextEntry::make('suami_istri')->label('Nama Suami / Istri')->inlineLabel(),
                                        TextEntry::make('alamat')->label('Alamat KTP')->inlineLabel()->columnSpanFull(),
                                        TextEntry::make('alamat_domisili')->label('Alamat Domisili')->inlineLabel()->columnSpanFull(),
                                        TextEntry::make('jarak_ke_kantor')->label('Jarak Rumah dari Kantor')->suffix(' KM')->inlineLabel(),
                                        TextEntry::make('user.email')->label('Email')->inlineLabel()->placeholder('-'),
                                    ]),
                            ]),

                        Tab::make('Data Kepegawaian')
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
                                                    ?->sortByDesc('tanggal')
                                                    ->first();

                                                return $latest?->tanggal
                                                    ? $latest->tanggal->translatedFormat('d F Y')
                                                    : '-';
                                            }),
                                        TextEntry::make('status')->label('Status')
                                            ->badge()
                                            ->color(fn($state) => match ($state) {
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
                                        RepeatableEntry::make('riwayatJabatans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal')->label('TMT')->date('d M Y'),
                                                TextEntry::make('nama_jabatan')->label('Jabatan'),
                                                TextEntry::make('file_sk')
                                                    ->label('Download SK')
                                                    ->formatStateUsing(fn() => 'Download')
                                                    ->url(fn($state) => \Illuminate\Support\Facades\Storage::url($state))
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->openUrlInNewTab()
                                                    ->badge()
                                                    ->color('success')
                                                    ->visible(fn($state) => !empty($state)),
                                            ])
                                            ->columns(3),
                                    ]),

                                Section::make('Riwayat Golongan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatGolongans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal')->label('TMT')->date('d M Y'),
                                                TextEntry::make('golongan.name')->label('Golongan'),
                                                TextEntry::make('file_sk')
                                                    ->label('Download SK')
                                                    ->formatStateUsing(fn() => 'Download')
                                                    ->url(fn($state) => \Illuminate\Support\Facades\Storage::url($state))
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->openUrlInNewTab()
                                                    ->badge()
                                                    ->color('success')
                                                    ->visible(fn($state) => !empty($state)),
                                            ])
                                            ->columns(3),
                                    ]),

                                Section::make('Riwayat Pendidikan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatPendidikans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('jenjang'),
                                                TextEntry::make('institusi'),
                                                TextEntry::make('jurusan')->placeholder('-'),
                                                TextEntry::make('tahun_lulus'),
                                                TextEntry::make('file_ijazah')
                                                    ->label('Ijazah')
                                                    ->formatStateUsing(fn() => 'Download')
                                                    ->url(fn($state) => \Illuminate\Support\Facades\Storage::url($state))
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->openUrlInNewTab()
                                                    ->badge()
                                                    ->color('success')
                                                    ->visible(fn($state) => !empty($state)),
                                            ])
                                            ->columns(5),
                                    ]),

                                Section::make('Riwayat Diklat/Pelatihan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatDiklats')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal_mulai')->label('Tanggal')->date('d M Y'),
                                                TextEntry::make('nama_diklat'),
                                                TextEntry::make('penyelenggara'),
                                                TextEntry::make('file_sertifikat')
                                                    ->label('Sertifikat')
                                                    ->formatStateUsing(fn() => 'Download')
                                                    ->url(fn($state) => \Illuminate\Support\Facades\Storage::url($state))
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->openUrlInNewTab()
                                                    ->badge()
                                                    ->color('success')
                                                    ->visible(fn($state) => !empty($state)),
                                            ])
                                            ->columns(4),
                                    ]),

                                Section::make('Riwayat Penghargaan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatPenghargaans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal')->label('Tanggal')->date('d M Y'),
                                                TextEntry::make('nama_penghargaan'),
                                                TextEntry::make('pemberi'),
                                                TextEntry::make('file_sertifikat')
                                                    ->label('Sertifikat')
                                                    ->formatStateUsing(fn() => 'Download')
                                                    ->url(fn($state) => \Illuminate\Support\Facades\Storage::url($state))
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->openUrlInNewTab()
                                                    ->badge()
                                                    ->color('success')
                                                    ->visible(fn($state) => !empty($state)),
                                            ])
                                            ->columns(4),
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
