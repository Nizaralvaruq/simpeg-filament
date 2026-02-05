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
                                        TextEntry::make('jabatan')->label('Amanah/Jabatan')->inlineLabel(),
                                        TextEntry::make('tmt_awal')->label('Mulai Bertugas')->date('d F Y')->inlineLabel(),
                                        TextEntry::make('masa_kerja')
                                            ->label('Masa Kerja')
                                            ->inlineLabel()
                                            ->state(function ($record) {
                                                if (!$record->tmt_awal) return '-';

                                                $start = $record->tmt_awal;
                                                $end = ($record->status === 'Resign' && $record->resignation?->tanggal_resign)
                                                    ? $record->resignation->tanggal_resign
                                                    : now();

                                                $diff = $start->diff($end);

                                                $parts = [];
                                                if ($diff->y > 0) $parts[] = $diff->y . ' Tahun';
                                                if ($diff->m > 0) $parts[] = $diff->m . ' Bulan';

                                                return count($parts) > 0 ? implode(' ', $parts) : 'Kurang dari 1 bulan';
                                            }),
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

                                        TextEntry::make('resignation.tanggal_resign')
                                            ->label('Tanggal Resign')
                                            ->date('d F Y')
                                            ->inlineLabel()
                                            ->visible(fn($record) => $record->status === 'Resign'),

                                        TextEntry::make('resignation.alasan')
                                            ->label('Alasan Resign')
                                            ->inlineLabel()
                                            ->visible(fn($record) => $record->status === 'Resign'),
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

                        Tab::make('Data Keluarga')
                            ->visible(fn($record) => filled($record->status_perkawinan) && $record->status_perkawinan !== 'Belum Menikah')
                            ->schema([
                                Section::make('Data Pasangan (Suami/Istri)')
                                    ->schema([
                                        RepeatableEntry::make('riwayatPasangan')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('nama')
                                                    ->weight('bold')
                                                    ->suffix(fn($record) => $record->tanggal_lahir ? ' (' . $record->tanggal_lahir->age . ' Thn)' : ''),
                                                TextEntry::make('nik')->label('NIK')->placeholder('-'),
                                                TextEntry::make('tempat_lahir')->label('Tempat Lahir')->placeholder('-'),
                                                TextEntry::make('tanggal_lahir')->label('Tgl Lahir')->date('d M Y')->placeholder('-'),
                                                TextEntry::make('pekerjaan')->placeholder('-'),
                                                TextEntry::make('no_hp')->label('No HP/WA')->placeholder('-'),
                                                TextEntry::make('file_kk')
                                                    ->label('KK/Akte')
                                                    ->formatStateUsing(fn() => 'Lihat')
                                                    ->url(fn($state) => \Illuminate\Support\Facades\Storage::url($state))
                                                    ->icon('heroicon-o-eye')
                                                    ->openUrlInNewTab()
                                                    ->badge()
                                                    ->color('info')
                                                    ->visible(fn($state) => !empty($state)),
                                            ])
                                            ->columns(5),
                                    ]),

                                Section::make('Data Anak')
                                    ->schema([
                                        RepeatableEntry::make('riwayatAnaks')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('nama')
                                                    ->weight('bold')
                                                    ->suffix(fn($record) => $record->tanggal_lahir ? ' (' . $record->tanggal_lahir->age . ' Thn)' : ''),
                                                TextEntry::make('nik')->label('NIK')->placeholder('-'),
                                                TextEntry::make('tempat_lahir')->label('Tempat Lahir')->placeholder('-'),
                                                TextEntry::make('tanggal_lahir')->label('Tgl Lahir')->date('d M Y')->placeholder('-'),
                                                TextEntry::make('pendidikan')->placeholder('-'),
                                                TextEntry::make('pekerjaan')->placeholder('-'),
                                                TextEntry::make('file_kk')
                                                    ->label('KK/Akte')
                                                    ->formatStateUsing(fn() => 'Lihat')
                                                    ->url(fn($state) => \Illuminate\Support\Facades\Storage::url($state))
                                                    ->icon('heroicon-o-eye')
                                                    ->openUrlInNewTab()
                                                    ->badge()
                                                    ->color('info')
                                                    ->visible(fn($state) => !empty($state)),
                                            ])
                                            ->columns(5),
                                    ]),
                            ]),

                        Tab::make('Riwayat Kepegawaian')
                            ->schema([
                                Section::make('Riwayat Amanah/Jabatan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatJabatans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal')->label('TMT')->date('d M Y'),
                                                TextEntry::make('nama_jabatan')->label('Amanah/Jabatan'),
                                                TextEntry::make('unit.name')->label('Unit'),
                                                TextEntry::make('nomor_sk')->label('Nomor SK'),
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
                                            ->columns(4),
                                    ]),

                                Section::make('Riwayat Golongan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatGolongans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal')->label('TMT')->date('d M Y'),
                                                TextEntry::make('golongan.name')->label('Golongan'),
                                                TextEntry::make('nomor_sk')->label('Nomor SK'),
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
                                            ->columns(4),
                                    ]),

                                Section::make('Riwayat Pendidikan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatPendidikans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('jenjang'),
                                                TextEntry::make('gelar')->label('Gelar')->placeholder('-'),
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
                                            ->columns(6),
                                    ]),

                                Section::make('Riwayat Diklat/Pelatihan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatDiklats')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal_mulai')->label('Tanggal')->date('d M Y'),
                                                TextEntry::make('nama_diklat'),
                                                TextEntry::make('nomor_sertifikat')->label('No. Sertifikat'),
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
                                            ->columns(5),
                                    ]),

                                Section::make('Riwayat Penghargaan')
                                    ->schema([
                                        RepeatableEntry::make('riwayatPenghargaans')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('tanggal')->label('Tanggal')->date('d M Y'),
                                                TextEntry::make('nama_penghargaan'),
                                                TextEntry::make('nomor_sertifikat')->label('No. Piagam'),
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
                                            ->columns(5),
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
