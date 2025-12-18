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
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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

    public static function getModelLabel(): string
    {
        return 'Pegawai';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pegawai';
    }

    /**
     * Query berdasarkan role
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        if (! $user) {
            return $query->whereRaw('1=0');
        }

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasAnyRole(['kepala_sekolah', 'koor_jenjang'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->all();

                return $query->whereHas('units', fn ($q) =>
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
        return $schema->components([
            Wizard::make([

                // Data Diri
                Step::make('Data Diri')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No HP')
                            ->tel(),

                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir'),

                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->displayFormat('d/m/Y'),

                        Forms\Components\Select::make('pendidikan')
                            ->label('Pendidikan')
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
                            ->label('Instansi'),

                        Forms\Components\Select::make('status_perkawinan')
                            ->label('Status Perkawinan')
                            ->options([
                                'Belum Menikah' => 'Belum Menikah',
                                'Menikah'       => 'Menikah',
                                'Cerai Hidup'   => 'Cerai Hidup',
                                'Cerai Mati'    => 'Cerai Mati',
                            ])
                            ->native(false),

                        Forms\Components\TextInput::make('suami_istri')
                            ->label('Suami / Istri'),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                // Data Induk
                Step::make('Data Induk')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('tmt_awal')
                            ->label('Mulai Bertugas')
                            ->displayFormat('d/m/Y'),

                        Forms\Components\TextInput::make('jabatan')
                            ->label('Jabatan Saat Ini')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('golongan_id')
                            ->label('Golongan Saat Ini')
                            ->relationship('golongan', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

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
                            ]),
                    ])
                    ->columns(3),

                // Riwayat
                Step::make('Riwayat Kepegawaian')
                    ->schema([

                        // PILIHAN STATUS PINDAH TUGAS
                        Forms\Components\Select::make('pindah_tugas')
                            ->label('Riwayat Tugas')
                            ->options([
                                'pernah' => 'Pernah pindah tugas',
                                'tetap'  => 'Tetap pada amanahnya',
                            ])
                            ->native(false)
                            ->required()
                            ->live() 
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state === 'tetap') {
                                    $set('riwayatJabatans', []);
                                }
                            }),

                        // Riwayat Jabatan (muncul hanya kalau "pernah pindah tugas")
                        Forms\Components\Repeater::make('riwayatJabatans')
                            ->relationship()
                            ->label('Riwayat Jabatan')
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal')
                                    ->required()
                                    ->displayFormat('d/m/Y'),

                                Forms\Components\TextInput::make('nama_jabatan')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->visible(fn (Get $get) => $get('status_pindah_tugas') === 'pernah'),

                        // Riwayat Golongan (kalau mau tetap selalu tampil, biarkan seperti ini)
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
                            ])
                            ->columns(2)
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (! is_array($state)) return;

                                $latest = collect($state)
                                    ->filter(fn ($row) => ! empty($row['tanggal']) && ! empty($row['golongan_id']))
                                    ->sortByDesc('tanggal')
                                    ->first();

                                if ($latest) {
                                    $set('golongan_id', $latest['golongan_id']);
                                    $set('tmt_akhir', $latest['tanggal']);
                                }
                            }),
                    ])
                    ->columns(1),

                // BPJS
                Step::make('BPJS')
                    ->schema([
                        Forms\Components\TextInput::make('no_bpjs')->label('Nomor BPJS'),
                        Forms\Components\TextInput::make('no_kjp_2p')->label('Nomor KJP 2P'),
                        Forms\Components\TextInput::make('no_kjp_3p')->label('Nomor KJP 3P'),
                    ])
                    ->columns(2),

                Step::make('Buat Akun Login')
                    ->description('Opsional')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email Login')
                            ->email()
                            ->dehydrated(false)
                            ->unique('users', 'email', ignoreRecord: true),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrated(false)
                            ->requiredWith('email'),
                    ])
                    ->columns(2),
            ])
                ->columnSpanFull()
                ->persistStepInQueryString(),
        ]);
    }

    /**
     * TABLE
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')->label('No')->rowIndex(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                // Tables\Columns\TextColumn::make('tmt_awal')->date('d M Y'),
                Tables\Columns\TextColumn::make('units.name')->badge()->separator(', '),
                Tables\Columns\TextColumn::make('jabatan'),
                Tables\Columns\TextColumn::make('golongan.name')->badge(),
            ])
            ->actions([ActionGroup::make([
                    Action::make('info')
                        ->label('Detail')
                        ->icon('heroicon-o-information-circle')
                        ->url(fn ($record) => self::getUrl('view', ['record' => $record])),

                    EditAction::make()
                        ->label('Edit'),

                    DeleteAction::make()
                        ->label('Hapus'),
                ])->label('Aksi')
            ])
            ->actionsColumnLabel('Aksi');


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
