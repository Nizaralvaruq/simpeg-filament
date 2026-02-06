<?php

namespace Modules\Kepegawaian\Filament\Resources;

use Modules\Kepegawaian\Filament\Resources\DataIndukResource\Pages;
use Modules\Kepegawaian\Models\DataInduk;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Modules\Kepegawaian\Exports\DataIndukExport;

class DataIndukResource extends Resource
{
    protected static ?string $model = DataInduk::class;
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-user';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Pegawai';
    }

    public static function getNavigationLabel(): string
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user && $user->hasRole('staff')) {
            return 'Biodata Saya';
        }

        return 'Data Pegawai';
    }

    public static function getModelLabel(): string
    {
        return 'Data Pegawai';
    }

    /**
     * Badge pada menu navigasi
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('status', 'Aktif')->count();
    }

    /**
     * Tooltip untuk badge navigasi
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total pegawai aktif';
    }

    /**
     * Warna badge navigasi
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    /**
     * Query berdasarkan role
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['units', 'golongan']);
        /** @var \App\Models\User $user */
        $user  = Auth::user();

        if (! $user) {
            return $query->whereRaw('1=0');
        }

        if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return $query;
        }

        if ($user->hasAnyRole(['kepala_sekolah', 'koor_jenjang', 'admin_unit'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->all();

                return $query->whereHas(
                    'units',
                    fn($q) =>
                    $q->whereIn('units.id', $unitIds)
                );
            }

            return $query->whereRaw('1=0');
        }

        if ($user->hasRole('staff')) {
            return $user->employee
                ? $query->where('id', $user->employee->id)
                : $query->whereRaw('1=0');
        }

        return $query->whereRaw('1=0');
    }

    /**
     * FORM
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components(function ($record) {
            $operation = $record ? 'edit' : 'create';
            $sections = [
                'Data Diri' => static::getBiodataSchema(),
                'Data Induk' => static::getKepegawaianSchema(),
                'Riwayat' => static::getRiwayatSchema(),
                'BPJS' => static::getBpjsSchema(),
                'Akun' => static::getAkunLoginSchema(),
            ];

            if ($operation === 'create') {
                return [
                    Wizard::make([
                        Step::make('Biodata')->schema($sections['Data Diri']),
                        Step::make('Pekerjaan')->schema($sections['Data Induk']),
                        Step::make('Riwayat')->schema($sections['Riwayat']),
                        Step::make('BPJS & Akun')->schema(array_merge($sections['BPJS'], $sections['Akun'])),
                    ])->columnSpanFull()
                ];
            }

            return [
                Tabs::make('Data Pegawai')
                    ->tabs([
                        Tab::make('Data Diri')->schema($sections['Data Diri']),
                        Tab::make('Data Induk')->schema($sections['Data Induk']),
                        Tab::make('Riwayat Kepegawaian')->schema($sections['Riwayat']),
                        Tab::make('BPJS & Kesejahteraan')->schema($sections['BPJS']),
                        Tab::make('Akses Aplikasi')->schema($sections['Akun']),
                    ])
                    ->columnSpanFull(),
            ];
        });
    }

    protected static function getBiodataSchema(): array
    {
        return [
            Section::make('Identitas Diri')
                ->schema([
                    Forms\Components\FileUpload::make('foto_profil')
                        ->label('Foto Profil (Pas Foto)')
                        ->image()
                        ->avatar()
                        ->imageEditor()
                        ->circleCropper()
                        ->disk('public')
                        ->directory('foto-profil')
                        ->maxSize(100)
                        ->getUploadedFileNameForStorageUsing(
                            fn(TemporaryUploadedFile $file): string =>
                            'Foto_Profil_' . now()->timestamp . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension()
                        )
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'justify-center']),

                    Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('nama')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Set $set, Get $get, $record) {
                                    if (!$record) {
                                        if (empty($get('email')) && !empty($state)) {
                                            $set('email', str($state)->lower()->replace(' ', '.')->append('@domain.com')->toString());
                                        }
                                        if (empty($get('password'))) {
                                            $set('password', 'password123');
                                        }
                                    }
                                }),
                            Forms\Components\TextInput::make('nik')
                                ->label('NIK')
                                ->mask('9999999999999999')
                                ->rules(fn($record) => [
                                    Rule::unique('data_induks', 'nik')->ignore($record?->id),
                                    Rule::unique('riwayat_keluargas', 'nik'),
                                ])
                                ->live(onBlur: true)
                                ->helperText('Harus 16 digit angka')
                                ->validationMessages([
                                    'unique' => 'NIK sudah terdaftar di sistem (Pegawai/Keluarga).',
                                ]),
                        ])->columns(2),

                    Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('tempat_lahir')->label('Tempat Lahir'),
                            Forms\Components\DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->displayFormat('d/m/Y'),
                            Forms\Components\Select::make('jenis_kelamin')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'Laki-laki' => 'Laki-laki',
                                    'Perempuan' => 'Perempuan',
                                ])
                                ->native(false),
                        ])->columns(3),

                    Group::make()
                        ->schema([
                            Forms\Components\Select::make('agama')
                                ->label('Agama')
                                ->options([
                                    'Islam' => 'Islam',
                                    'Kristen' => 'Kristen',
                                    'Katolik' => 'Katolik',
                                    'Hindu' => 'Hindu',
                                    'Buddha' => 'Buddha',
                                    'Konghucu' => 'Konghucu',
                                ])
                                ->native(false),
                            Forms\Components\Select::make('golongan_darah')
                                ->label('Golongan Darah')
                                ->options([
                                    'A' => 'A',
                                    'B' => 'B',
                                    'AB' => 'AB',
                                    'O' => 'O',
                                ])
                                ->native(false),
                            Forms\Components\TextInput::make('no_hp')
                                ->label('No HP / WhatsApp')
                                ->tel()
                                ->numeric(),
                        ])->columns(3),
                ]),

            Section::make('Pendidikan & Keluarga')
                ->collapsible()
                ->schema([
                    Group::make()
                        ->schema([
                            Forms\Components\Select::make('pendidikan')
                                ->label('Pendidikan Terakhir')
                                ->options([
                                    'SMA' => 'SMA',
                                    'D1'  => 'D1',
                                    'D3'  => 'D3',
                                    'D4'  => 'D4',
                                    'S1'  => 'S1',
                                    'S2'  => 'S2',
                                    'S3'  => 'S3',
                                ])
                                ->searchable()
                                ->native(false),
                            Forms\Components\TextInput::make('instansi')
                                ->label('Nama Sekolah / Universitas'),
                        ])->columns(2),

                    Group::make()
                        ->schema([
                            Forms\Components\Select::make('status_perkawinan')
                                ->label('Status Perkawinan')
                                ->options([
                                    'Belum Menikah' => 'Belum Menikah',
                                    'Menikah'       => 'Menikah',
                                    'Cerai Hidup'   => 'Cerai Hidup',
                                    'Cerai Mati'    => 'Cerai Mati',
                                ])
                                ->native(false)
                                ->live(),
                        ])->columns(1),

                    Forms\Components\Repeater::make('riwayatPasangan')
                        ->relationship()
                        ->label('Data Pasangan (Suami/Istri)')
                        ->visible(fn(Get $get) => filled($get('status_perkawinan')) && $get('status_perkawinan') !== 'Belum Menikah')
                        ->schema([
                            Forms\Components\TextInput::make('nama')
                                ->required()
                                ->columnSpan(2),
                            Forms\Components\Select::make('hubungan')
                                ->options([
                                    'Suami' => 'Suami',
                                    'Istri' => 'Istri',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('nik')
                                ->label('NIK')
                                ->mask('9999999999999999')
                                ->distinct()
                                ->rules(fn($record) => [
                                    Rule::unique('riwayat_keluargas', 'nik')->ignore($record?->id),
                                    Rule::unique('data_induks', 'nik'),
                                ])
                                ->helperText('16 digit')
                                ->validationMessages([
                                    'unique' => 'NIK sudah terdaftar (Pegawai/Keluarga).',
                                ]),
                            Forms\Components\TextInput::make('tempat_lahir')
                                ->label('Tempat Lahir'),
                            Forms\Components\DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->displayFormat('d/m/Y'),
                            Forms\Components\TextInput::make('pekerjaan')
                                ->label('Pekerjaan'),
                            Forms\Components\TextInput::make('no_hp')
                                ->label('No HP / WA')
                                ->tel()
                                ->placeholder('Kontak Darurat'),
                            Forms\Components\FileUpload::make('file_kk')
                                ->label('Upload KK/Akte')
                                ->disk('public')
                                ->directory('dokumen-keluarga')
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => ($state['nama'] ?? '') . ' (' . ($state['hubungan'] ?? '') . ')')
                        ->defaultItems(0),

                    Forms\Components\Repeater::make('riwayatAnaks')
                        ->relationship()
                        ->label('Data Anak')
                        ->visible(fn(Get $get) => filled($get('status_perkawinan')) && $get('status_perkawinan') !== 'Belum Menikah')
                        ->schema([
                            Forms\Components\TextInput::make('nama')
                                ->required()
                                ->columnSpan(2),
                            Forms\Components\Hidden::make('hubungan')
                                ->default('Anak'),
                            Forms\Components\TextInput::make('nik')
                                ->label('NIK')
                                ->mask('9999999999999999')
                                ->distinct()
                                ->rules(fn($record) => [
                                    Rule::unique('riwayat_keluargas', 'nik')->ignore($record?->id),
                                    Rule::unique('data_induks', 'nik'),
                                ])
                                ->helperText('16 digit')
                                ->validationMessages([
                                    'unique' => 'NIK sudah terdaftar (Pegawai/Keluarga).',
                                ]),
                            Forms\Components\TextInput::make('tempat_lahir')
                                ->label('Tempat Lahir'),
                            Forms\Components\DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->displayFormat('d/m/Y'),
                            Forms\Components\TextInput::make('pekerjaan')
                                ->label('Pekerjaan'),
                            Forms\Components\Select::make('pendidikan')
                                ->label('Pendidikan')
                                ->options([
                                    'Belum Sekolah' => 'Belum Sekolah',
                                    'TK'            => 'TK',
                                    'SD'            => 'SD',
                                    'SMP'           => 'SMP',
                                    'SMA'           => 'SMA',
                                    'Kuliah'        => 'Kuliah',
                                ])
                                ->native(false),
                            Forms\Components\FileUpload::make('file_kk')
                                ->label('Upload KK/Akte')
                                ->disk('public')
                                ->directory('dokumen-keluarga')
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['nama'] ?? null)
                        ->defaultItems(0),
                ]),

            Section::make('Alamat & Domisili')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Group::make()
                        ->schema([
                            Forms\Components\Textarea::make('alamat')
                                ->label('Alamat Sesuai KTP')
                                ->rows(3),
                            Forms\Components\Textarea::make('alamat_domisili')
                                ->label('Alamat Domisili (jika beda)')
                                ->rows(3),
                        ])->columns(2),
                    Forms\Components\TextInput::make('jarak_ke_kantor')
                        ->label('Jarak Rumah dari Kantor')
                        ->numeric()
                        ->suffix('KM')
                        ->minValue(0),
                ]),
        ];
    }

    protected static function getKepegawaianSchema(): array
    {
        return [
            Section::make('Informasi Kepegawaian')
                ->collapsible()
                ->schema([
                    Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('nip')
                                ->label('NPA (Nomor Pokok Anggota)')
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            Forms\Components\DatePicker::make('tmt_awal')
                                ->label('Tanggal Mulai Bertugas')
                                ->displayFormat('d/m/Y'),
                        ])->columns(2),

                    Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('jabatan')
                                ->label('Amanah/Jabatan Saat Ini')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('golongan_id')
                                ->label('Golongan Saat Ini')
                                ->relationship('golongan', 'name')
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->required(),
                        ])->columns(2),

                    Group::make()
                        ->schema([
                            Forms\Components\Select::make('units')
                                ->label('Unit Kerja')
                                ->relationship('units', 'name')
                                ->multiple()
                                ->preload(),
                            Forms\Components\Select::make('status_kepegawaian')
                                ->label('Status Kepegawaian')
                                ->options([
                                    'Tetap'   => 'Tetap',
                                    'Kontrak' => 'Kontrak',
                                    'Magang'  => 'Magang',
                                ])
                                ->native(false),
                            Forms\Components\Select::make('status')
                                ->label('Status Aktif')
                                ->options([
                                    'Aktif'  => 'Aktif',
                                    'Cuti'   => 'Cuti',
                                    'Resign' => 'Resign',
                                ])
                                ->native(false)
                                ->required()
                                ->live(),
                        ])->columns(2),
                ]),
        ];
    }

    protected static function getRiwayatSchema(): array
    {
        return [
            Section::make('Status Mutasi / Amanah / Jabatan')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Select::make('pindah_tugas')
                        ->label('Riwayat Tugas')
                        ->options([
                            'pernah' => 'Pernah pindah tugas',
                            'tetap'  => 'Tetap',
                        ])
                        ->native(false)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state === 'tetap') {
                                $set('riwayatJabatans', []);
                            }
                        }),

                    Forms\Components\Repeater::make('riwayatJabatans')
                        ->relationship()
                        ->label('List Riwayat Amanah/Jabatan')
                        ->schema([
                            Forms\Components\DatePicker::make('tanggal')
                                ->required()
                                ->displayFormat('d/m/Y'),
                            Forms\Components\TextInput::make('nama_jabatan')
                                ->label('Amanah/Jabatan')
                                ->required(),
                            Forms\Components\Select::make('unit_id')
                                ->relationship('unit', 'name')
                                ->label('Unit Kerja')
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('nomor_sk')
                                ->label('Nomor SK'),
                            Forms\Components\FileUpload::make('file_sk')
                                ->label('File SK (PDF/Image)')
                                ->disk('public')
                                ->directory('sk-jabatan')
                                ->maxSize(1024)
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string =>
                                    'SK_Jabatan_' . now()->timestamp . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension()
                                )
                                ->columnSpanFull(),
                        ])
                        ->columns(4)
                        ->visible(fn(Get $get) => $get('pindah_tugas') === 'pernah')
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (! is_array($state)) return;
                            $latest = collect($state)
                                ->filter(fn($r) => ! empty($r['tanggal']) && ! empty($r['nama_jabatan']))
                                ->sortByDesc('tanggal')
                                ->first();
                            if ($latest) {
                                $set('jabatan', $latest['nama_jabatan']);
                            }
                        }),
                ]),

            Section::make('Riwayat Golongan & Pendidikan')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Repeater::make('riwayatGolongans')
                        ->relationship()
                        ->label('Riwayat Golongan')
                        ->live()
                        ->schema([
                            Forms\Components\DatePicker::make('tanggal')
                                ->required()
                                ->displayFormat('d/m/Y'),
                            Forms\Components\Select::make('golongan_id')
                                ->required()
                                ->relationship('golongan', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('nomor_sk')
                                ->label('Nomor SK'),
                            Forms\Components\FileUpload::make('file_sk')
                                ->label('File SK (PDF/Image)')
                                ->disk('public')
                                ->maxSize(1024)
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string =>
                                    'SK_Golongan_' . now()->timestamp . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension()
                                )
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (! is_array($state)) return;
                            $latest = collect($state)
                                ->filter(fn($row) => ! empty($row['tanggal']) && ! empty($row['golongan_id']))
                                ->sortByDesc('tanggal')
                                ->first();
                            if ($latest) {
                                $set('golongan_id', $latest['golongan_id']);
                                $set('tmt_akhir', $latest['tanggal']);
                            }
                        }),

                    Forms\Components\Repeater::make('riwayatPendidikans')
                        ->relationship()
                        ->label('Riwayat Pendidikan')
                        ->schema([
                            Forms\Components\Select::make('jenjang')
                                ->options([
                                    'SD' => 'SD',
                                    'SMP' => 'SMP',
                                    'SMA' => 'SMA/SMK',
                                    'D1' => 'D1',
                                    'D2' => 'D2',
                                    'D3' => 'D3',
                                    'S1' => 'S1',
                                    'S2' => 'S2',
                                    'S3' => 'S3',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('gelar')
                                ->label('Gelar (contoh: S.Kom)'),
                            Forms\Components\TextInput::make('institusi')
                                ->label('Nama Institusi')
                                ->required(),
                            Forms\Components\TextInput::make('jurusan')
                                ->label('Jurusan'),
                            Forms\Components\TextInput::make('tahun_lulus')
                                ->label('Tahun Lulus')
                                ->numeric()
                                ->required(),
                            Forms\Components\FileUpload::make('file_ijazah')
                                ->label('File Ijazah (PDF/Image)')
                                ->disk('public')
                                ->directory('ijazah')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->maxSize(1024)
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string =>
                                    'Ijazah_' . now()->timestamp . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension()
                                )
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ]),

            Section::make('Riwayat Pengembangan Diri & Penghargaan')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Repeater::make('riwayatDiklats')
                        ->relationship()
                        ->label('Riwayat Diklat/Pelatihan')
                        ->schema([
                            Forms\Components\TextInput::make('nama_diklat')
                                ->label('Nama Diklat')
                                ->required(),
                            Forms\Components\TextInput::make('nomor_sertifikat')
                                ->label('Nomor Sertifikat'),
                            Forms\Components\TextInput::make('penyelenggara')
                                ->required(),
                            Forms\Components\DatePicker::make('tanggal_mulai')
                                ->label('Tanggal Mulai')
                                ->required(),
                            Forms\Components\DatePicker::make('tanggal_selesai')
                                ->label('Tanggal Selesai'),
                            Forms\Components\TextInput::make('durasi_jam')
                                ->label('Durasi (Jam)')
                                ->numeric(),
                            Forms\Components\FileUpload::make('file_sertifikat')
                                ->label('Sertifikat (PDF/Image)')
                                ->disk('public')
                                ->directory('sertifikat-diklat')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->maxSize(1024)
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string =>
                                    'Sertifikat_Diklat_' . now()->timestamp . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension()
                                )
                                ->columnSpanFull(),
                        ])
                        ->columns(3),

                    Forms\Components\Repeater::make('riwayatPenghargaans')
                        ->relationship()
                        ->label('Riwayat Penghargaan')
                        ->schema([
                            Forms\Components\TextInput::make('nama_penghargaan')
                                ->label('Nama Penghargaan')
                                ->required(),
                            Forms\Components\TextInput::make('nomor_sertifikat')
                                ->label('Nomor Piagam/Sertifikat'),
                            Forms\Components\TextInput::make('pemberi')
                                ->label('Instansi Pemberi')
                                ->required(),
                            Forms\Components\DatePicker::make('tanggal')
                                ->required(),
                            Forms\Components\FileUpload::make('file_sertifikat')
                                ->label('Sertifikat (PDF/Image)')
                                ->disk('public')
                                ->directory('sertifikat-penghargaan')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->maxSize(1024)
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string =>
                                    'Sertifikat_Penghargaan_' . now()->timestamp . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension()
                                )
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ]),
        ];
    }

    protected static function getBpjsSchema(): array
    {
        return [
            Section::make('Informasi BPJS')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('no_bpjs')->label('Nomor BPJS Kesehatan/Ketenagakerjaan'),
                    Forms\Components\TextInput::make('no_kjp_2p')->label('Nomor KJP 2P'),
                    Forms\Components\TextInput::make('no_kjp_3p')->label('Nomor KJP 3P'),
                ])->columns(2),
        ];
    }

    protected static function getAkunLoginSchema(): array
    {
        return [
            Section::make('Akun Login')
                ->collapsible()
                ->collapsed()
                ->description('Isi Email & Password di bawah jika ingin membuat akun baru untuk pegawai ini.')
                ->visible(function (): bool {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    return $user?->hasAnyRole(['super_admin', 'admin_unit', 'ketua_psdm']);
                })
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('Email Login')
                        ->email()
                        ->dehydrated(false)
                        ->placeholder('contoh: budi.santoso@domain.com')
                        ->autocomplete('off')
                        ->afterStateHydrated(function (Forms\Components\TextInput $component, $record) {
                            if ($record && $record->user) {
                                $component->state($record->user->email);
                            }
                        })
                        ->rules([
                            fn($record) => \Illuminate\Validation\Rule::unique('users', 'email')->ignore($record?->user_id),
                        ]),

                    Forms\Components\Select::make('roles')
                        ->label('Roles')
                        ->options(\Spatie\Permission\Models\Role::pluck('name', 'name'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->dehydrated(false)
                        ->afterStateHydrated(function (Forms\Components\Select $component, $record) {
                            if ($record && $record->user) {
                                $component->state($record->user->roles->pluck('name'));
                            }
                        }),

                    Forms\Components\TextInput::make('password')
                        ->label('Password Baru')
                        ->password()
                        ->dehydrated(false)
                        ->autocomplete('new-password')
                        ->placeholder('Min. 8 karakter (Kosongkan jika tidak ingin mengubah)')
                        ->minLength(8)
                        ->validationMessages([
                            'min' => 'Password harus minimal 8 karakter.',
                        ])
                        ->requiredWith('email'),
                ])
                ->columns(2),
        ];
    }

    /**
     * TABLE
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')->label('No')->rowIndex(),
                Tables\Columns\ImageColumn::make('foto_profil')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('units.name')
                    ->label('Unit Kerja')
                    ->badge()
                    ->separator(', ')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Amanah/Jabatan')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('golongan.name')
                    ->label('Golongan')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Aktif' => 'success',
                        'Cuti' => 'warning',
                        'Resign' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->defaultPaginationPageOption(10)
            ->persistFiltersInSession()
            ->filters([
                Tables\Filters\SelectFilter::make('units')
                    ->label('Unit Kerja')
                    ->relationship('units', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pegawai')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Cuti' => 'Cuti',
                        'Resign' => 'Resign',
                    ]),

                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),

                Tables\Filters\SelectFilter::make('golongan')
                    ->label('Golongan')
                    ->relationship('golongan', 'name'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('info')
                        ->label('Detail')
                        ->icon('heroicon-o-information-circle')
                        ->url(
                            fn($record) => self::getUrl('view', ['record' => $record])
                        ),
                    Action::make('export')
                        ->label('Export Biodata')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(
                            fn($record) =>
                            FacadesExcel::download(
                                new DataIndukExport($record),
                                'biodata-' . str($record->nama)->slug() . '.xlsx'
                            )
                        ),
                    EditAction::make()
                        ->label('Ubah'),

                    DeleteAction::make()
                        ->label('Hapus'),
                ])->label('Aksi')
            ])
            ->recordActionsColumnLabel('Aksi')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export')
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(
                            fn(
                                mixed $records
                            ) =>
                            FacadesExcel::download(
                                new DataIndukExport($records),
                                'pegawai-terpilih.xlsx'
                            )
                        ),
                ]),
            ])
            ->recordUrl(
                fn($record) => self::getUrl('view', ['record' => $record])
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDataInduks::route('/'),
            'create' => Pages\CreateDataInduk::route('/create'),
            'view'   => Pages\ViewDataInduk::route('/{record}'),
            'edit'   => Pages\EditDataInduk::route('/{record}/edit'),
        ];
    }
}
