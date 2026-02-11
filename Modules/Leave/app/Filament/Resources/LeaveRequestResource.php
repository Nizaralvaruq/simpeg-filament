<?php

namespace Modules\Leave\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Modules\Leave\Filament\Resources\LeaveRequestResource\Pages;
use Modules\Leave\Models\LeaveRequest;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function getModelLabel(): string
    {
        return 'Permohonan Izin';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Permohonan Izin';
    }

    /**
     * Badge pada menu navigasi
     */
    public static function getNavigationBadge(): ?string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user?->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
            return null;
        }

        $query = static::getModel()::where('status', 'pending');

        // Admin Unit: Only count pending requests from their units
        if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                return null; // No units assigned
            }
        }

        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    /**
     * Tooltip untuk badge navigasi
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Permohonan izin menunggu persetujuan';
    }

    /**
     * Warna badge navigasi
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['employee']);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Super Admin & Ketua PSDM: View ALL
        if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return $query;
        }

        if ($user->hasAnyRole(['kepala_sekolah', 'koor_jenjang', 'admin_unit'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                return $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }

            return $query->whereRaw('1=0');
        }

        // Staff: View Own Requests
        if ($user->hasRole('staff')) {
            return $query->whereHas('employee', fn($q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Detail Pengajuan')
                ->schema([

                    Forms\Components\Select::make('data_induk_id')
                        ->label('Nama Pegawai')
                        ->relationship('employee', 'nama')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->visible(function () {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return $user?->hasRole('super_admin');
                        })
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('data_induk_id')
                        ->default(fn() => Auth::user()?->employee?->id)
                        ->visible(function () {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return ! $user?->hasRole('super_admin');
                        }),

                    Forms\Components\Select::make('leave_type')
                        ->label('Jenis Pengajuan')
                        ->options([
                            'cuti' => 'Cuti',
                            'sakit' => 'Sakit',
                            'izin' => 'Izin',
                        ])
                        ->required()
                        ->default('cuti'),

                    \Filament\Schemas\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('start_date')
                                ->label('Mulai Tanggal')
                                ->required()
                                ->minDate(now()),
                            Forms\Components\DatePicker::make('end_date')
                                ->label('Sampai Tanggal')
                                ->required()
                                ->afterOrEqual('start_date'),
                        ]),

                    Forms\Components\Textarea::make('reason')
                        ->label('Alasan')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('upload_file')
                        ->label('Upload Bukti (PDF/JPG/PNG)')
                        ->directory('upload_file')
                        ->disk('public')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(2048)
                        ->visible(function () {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return $user?->hasAnyRole(['staff', 'super_admin']);
                        })
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('status')
                        ->default('pending')
                        ->dehydrated(true)
                        ->visible(function () {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return ! $user?->hasAnyRole(['super_admin', 'admin']);
                        }),
                ]),

            \Filament\Schemas\Components\Section::make('Persetujuan')
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    return $user?->hasAnyRole(['super_admin', 'admin', 'ketua_psdm', 'koor_jenjang', 'kepala_sekolah']);
                })
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ])
                        ->required()
                        ->default('pending')
                        ->live(), // penting biar note ikut refresh

                    Forms\Components\Textarea::make('note')
                        ->label('Catatan / Alasan Penolakan')
                        ->visible(fn($get) => $get('status') === 'rejected')
                        ->required(fn($get) => $get('status') === 'rejected'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Nama Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('leave_type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cuti' => 'info',
                        'sakit' => 'danger',
                        'izin' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label('Mulai'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label('Selesai'),
                Tables\Columns\TextColumn::make('lama_cuti')
                    ->label('Durasi')
                    ->state(
                        fn($record) =>
                        \Carbon\Carbon::parse($record->start_date)
                            ->diffInDays(\Carbon\Carbon::parse($record->end_date)) + 1 . ' hari'
                    ),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('upload_file')
                    ->label('Bukti')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat/Download' : '-')
                    ->url(fn($record) => $record->upload_file ? asset('storage/' . $record->upload_file) : null, true)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color(fn($record) => $record->upload_file ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('keterangan_kembali')
                    ->label('Status Kembali')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        if ($record->status !== 'approved') {
                            return '-';
                        }
                        return $record->keterangan_kembali ?? 'belum kembali';
                    })
                    ->color(fn($state) => match ($state) {
                        'belum kembali' => 'warning',
                        'sudah kembali' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return $user?->hasAnyRole(['super_admin', 'admin', 'ketua_psdm', 'koor_jenjang', 'kepala_sekolah'])
                                && $record->status === 'pending';
                        })
                        ->action(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();

                            // 1. UPDATE CUTI
                            $record->update([
                                'status' => 'approved',
                                'note' => 'OK',
                                'approved_by' => $user->id,
                                'keterangan_kembali' => 'belum kembali',
                            ]);

                            // 2. UPDATE DATA INDUK
                            if ($record->employee) {
                                // Update status "live" pegawai
                                $record->employee->update([
                                    'status' => $record->leave_type, // cuti/sakit/izin
                                    'keterangan' => $record->reason,
                                ]);

                                // 3. CREATE ABSENSI RECORDS (SYNC)
                                $startDate = \Carbon\Carbon::parse($record->start_date);
                                $endDate = \Carbon\Carbon::parse($record->end_date);
                                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

                                foreach ($period as $date) {
                                    // Skip weekends (optional, but standard usually)
                                    // if ($date->isWeekend()) continue; 

                                    // Create/Update Absensi for this date
                                    if ($record->employee->user_id) {
                                        \Modules\Presensi\Models\Absensi::updateOrCreate(
                                            [
                                                'user_id' => $record->employee->user_id,
                                                'tanggal' => $date->format('Y-m-d'),
                                            ],
                                            [
                                                'status' => $record->leave_type, // cuti/sakit/izin
                                                'keterangan' => $record->reason,
                                                'jam_masuk' => null,
                                                'jam_keluar' => null,
                                            ]
                                        );
                                    }
                                }
                            }
                        }),

                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->schema([
                            Forms\Components\Textarea::make('note')
                                ->label('Catatan')
                                ->required(),
                        ])
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return $user?->hasAnyRole(['super_admin', 'admin', 'ketua_psdm', 'koor_jenjang', 'kepala_sekolah'])
                                && $record->status === 'pending';
                        })
                        ->action(fn($record, array $data) => $record->update([
                            'status' => 'rejected',
                            'note' => $data['note'],
                            'approved_by' => Auth::id(),
                        ])),

                    Action::make('kembali')
                        ->label('Sudah Kembali')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if (!$user) return false;

                            if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) return $record->status === 'approved' && in_array($record->keterangan_kembali, [null, 'belum kembali']);

                            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                                $managerUnitIds = $user->employee?->units->pluck('id')->all() ?? [];
                                $recordOwnerUnitIds = $record->employee?->units->pluck('id')->all() ?? [];

                                return !empty(array_intersect($managerUnitIds, $recordOwnerUnitIds))
                                    && $record->status === 'approved'
                                    && in_array($record->keterangan_kembali, [null, 'belum kembali']);
                            }

                            return false;
                        })
                        ->action(function ($record) {
                            // 1. UPDATE CUTI
                            $record->update([
                                'keterangan_kembali' => 'sudah kembali',
                            ]);
                            // 2. UPDATE DATA INDUK → AKTIF
                            if ($record->employee) {
                                $record->employee->update([
                                    'status' => 'aktif',
                                    'keterangan' => null,
                                ]);
                            }
                        }),

                    EditAction::make()
                        ->label('Ubah')
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();

                            if ($user?->hasAnyRole(['super_admin', 'admin'])) {
                                return true;
                            }

                            return $user?->hasRole('staff')
                                && $user->employee
                                && $record->data_induk_id === $user->employee->id
                                && $record->status === 'pending';
                        }),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return $user?->hasRole('super_admin') ||
                                ($user?->hasRole('staff') && $record->status === 'pending' && $record->data_induk_id === $user->employee?->id);
                        }),

                ])
                    ->icon('heroicon-o-ellipsis-vertical'),
            ])
            ->recordActionsColumnLabel('Aksi')
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
