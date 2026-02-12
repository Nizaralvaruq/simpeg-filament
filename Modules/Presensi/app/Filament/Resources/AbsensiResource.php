<?php

namespace Modules\Presensi\Filament\Resources;

use Modules\Presensi\Filament\Resources\AbsensiResource\Pages;
use Modules\Presensi\Models\Absensi;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

// Use Global Actions if Table Actions are not found

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Presensi';
    }

    public static function getNavigationLabel(): string
    {
        return 'Riwayat Absensi';
    }

    public static function getModelLabel(): string
    {
        return 'Absensi';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()
            ->where('status', 'alpha')
            ->where('tanggal', now()->toDateString())
            ->count();

        return $count > 0 ? (string)$count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah Alpha / Tanpa Keterangan Hari Ini';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user.employee.units']);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return $query;
        }

        if ($user->hasAnyRole(['kepala_sekolah', 'koor_jenjang', 'admin_unit'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->all();

                return $query->whereHas(
                    'user.employee.units',
                    fn($q) => $q->whereIn('units.id', $unitIds)
                );
            }

            // Optional: If no units assigned, usually they shouldn't see anything or just self
            // Following DataIndukResource standard:
            return $query->whereRaw('1=0');
        }

        // Staff sees only own data
        return $query->where('user_id', $user->id);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Absensi')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name', modifyQueryUsing: function (Builder $query) {
                                /** @var \App\Models\User $user */
                                $user = Auth::user();
                                if (!$user || $user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
                                    return $query;
                                }

                                if ($user->employee && $user->employee->units->isNotEmpty()) {
                                    $unitIds = $user->employee->units->pluck('id')->all();
                                    return $query->whereHas('employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                                }

                                return $query->where('id', $user->id);
                            })
                            ->default(Auth::id())
                            ->required()
                            ->label('Nama Pegawai')
                            ->disabled(function () {
                                /** @var \App\Models\User $user */
                                $user = Auth::user();
                                return ! ($user?->hasAnyRole(['super_admin', 'admin_unit', 'koor_jenjang', 'ketua_psdm', 'kepala_sekolah']) ?? false);
                            })
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now())
                            ->label('Tanggal')
                            ->disabled(function () {
                                /** @var \App\Models\User $user */
                                $user = Auth::user();
                                return ! ($user?->hasAnyRole(['super_admin', 'admin_unit', 'koor_jenjang', 'ketua_psdm', 'kepala_sekolah']) ?? false);
                            })
                            ->dehydrated(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'hadir' => 'Hadir',
                                'dinas_luar' => 'Dinas Luar',
                                'izin' =>  'Izin',
                                'sakit' => 'Sakit',
                                'cuti' => 'Cuti',
                                'alpha' => 'Alpha',
                            ])
                            ->required()
                            ->default('hadir')
                            ->native(false)
                            ->live(),

                        Forms\Components\TimePicker::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->default(now())
                            ->required(fn($get) => $get('status') === 'hadir')
                            ->visible(fn($get) => $get('status') === 'hadir'),

                        Forms\Components\TimePicker::make('jam_keluar')
                            ->label('Jam Keluar')
                            ->visible(fn($get) => $get('status') === 'hadir'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Alasan Izin/Sakit')
                            ->helperText('Berikan alasan singkat mengapa Anda izin atau sakit.')
                            ->required(fn($get) => in_array($get('status'), ['izin', 'sakit']))
                            ->visible(fn($get) => in_array($get('status'), ['izin', 'sakit']))
                            ->columnSpanFull(),

                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->modifyQueryUsing(fn($query) => $query->with(['user.employee.units']))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        'alpha' => 'gray',
                        'dinas_luar' => 'info',
                        'cuti' => 'primary',
                    })
                    ->formatStateUsing(fn(string $state): string => str($state)->replace('_', ' ')->title()),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->time(),

                Tables\Columns\TextColumn::make('jam_keluar')
                    ->time(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('-')
                    ->toggleable(),


                Tables\Columns\TextColumn::make('late_minutes')
                    ->label('Keterangan Terlambat')
                    ->state(function (Absensi $record) {
                        $late = $record->late_minutes;
                        return $late > 0 ? "Terlambat $late menit" : 'Tepat Waktu';
                    })
                    ->badge()
                    ->color(fn($state) => str_contains($state, 'Terlambat') ? 'danger' : 'success')
                    ->visible(fn($record) => $record?->status === 'hadir'),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->state(function ($record) {
                        return $record?->alamat_lokasi ?? ($record?->latitude ? "Maps" : "-");
                    })
                    ->url(
                        fn($record) => $record?->latitude && $record?->longitude
                            ? "https://www.google.com/maps?q={$record->latitude},{$record->longitude}"
                            : null
                    )
                    ->openUrlInNewTab()
                    ->color('info')
                    ->icon(fn($record) => $record?->latitude && $record?->longitude ? 'heroicon-o-map-pin' : null)
                    ->wrap()
                    ->limit(50),

                Tables\Columns\ImageColumn::make('foto_verifikasi')
                    ->label('Foto')
                    ->square()
                    ->imageSize(40)
                    ->visibility('public'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'dinas_luar' => 'Dinas Luar',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'cuti' => 'Cuti',
                        'alpha' => 'Alpha',
                    ]),
                Tables\Filters\Filter::make('tanggal')
                    ->schema([
                        Forms\Components\DatePicker::make('dari_tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date) => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date) => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Ubah')
                        ->visible(function (Absensi $record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if (!$user) return false;

                            if ($user->hasRole('super_admin')) return true;

                            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                                /** @var \App\Models\User $manager */
                                $manager = $user;
                                $managerUnitIds = $manager->employee?->units->pluck('id')->all() ?? [];
                                $recordOwnerUnitIds = $record->user?->employee?->units->pluck('id')->all() ?? [];

                                return !empty(array_intersect($managerUnitIds, $recordOwnerUnitIds));
                            }

                            // Staff can only edit their own
                            return $user->can('Update:Absensi') && $record->user_id === $user->id;
                        }),
                    DeleteAction::make()
                        ->label('Hapus')
                        ->visible(function (Absensi $record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if (!$user) return false;

                            if ($user->hasRole('super_admin')) return true;

                            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                                /** @var \App\Models\User $manager */
                                $manager = $user;
                                $managerUnitIds = $manager->employee?->units->pluck('id')->all() ?? [];
                                $recordOwnerUnitIds = $record->user?->employee?->units->pluck('id')->all() ?? [];

                                return !empty(array_intersect($managerUnitIds, $recordOwnerUnitIds));
                            }

                            // Staff can only delete their own
                            return $user->can('Delete:Absensi') && $record->user_id === $user->id;
                        }),
                ])->label('Aksi'),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make()
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $user?->hasRole('super_admin') ?? false;
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
