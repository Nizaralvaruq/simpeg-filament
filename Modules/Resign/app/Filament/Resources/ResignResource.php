<?php

namespace Modules\Resign\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Modules\Resign\Filament\Resources\ResignResource\Pages;
use Modules\Resign\Models\Resign;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ResignResource extends Resource
{
    protected static ?string $model = Resign::class;
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-arrow-left-on-rectangle';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan Resign';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan Resign';
    }

    /**
     * Badge pada menu navigasi
     */
    public static function getNavigationBadge(): ?string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user?->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit'])) {
            return null;
        }

        $count = static::getModel()::where('status', 'diajukan')->count();
        return $count > 0 ? (string) $count : null;
    }

    /**
     * Tooltip untuk badge navigasi
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pengajuan resign menunggu persetujuan';
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
        $query = parent::getEloquentQuery()->with(['employee.units']);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Super Admin & Ketua PSDM: View ALL
        if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return $query;
        }

        // 2. Kepala Sekolah, Koor Jenjang, Admin Unit: View Unit Resigns
        if ($user->hasAnyRole(['kepala_sekolah', 'koor_jenjang', 'admin_unit'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                return $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            return $query->whereRaw('1=0');
        }

        // 3. Staff: View Own Resigns
        if ($user->hasRole('staff')) {
            return $query->whereHas('employee', fn($q) => $q->where('user_id', $user->id));
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Pengajuan')
                    ->schema([
                        // Hidden field for employee ID, filled automatically on create
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

                        Forms\Components\DatePicker::make('tanggal_resign')
                            ->required()
                            ->label('Tanggal Resign')
                            ->minDate(now()),

                        Forms\Components\Textarea::make('alasan')
                            ->required()
                            ->label('Alasan Resign')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('status')
                            ->default('diajukan')
                            ->dehydrated(true)
                            ->visible(function () {
                                /** @var \App\Models\User $user */
                                $user = Auth::user();
                                return ! $user?->hasAnyRole(['super_admin', 'admin']);
                            }),

                        Forms\Components\FileUpload::make('upload_file')
                            ->label('Bukti (PDF/JPG/PNG)')
                            ->directory('upload_file')
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Persetujuan')
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $user?->hasAnyRole(['super_admin', 'admin', 'admin_unit']);
                    })
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'diajukan' => 'Diajukan',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Textarea::make('keterangan_tindak_lanjut')
                            ->label('Catatan / Alasan Penolakan')
                            ->visible(fn($get) => $get('status') === 'ditolak')
                            ->required(fn($get) => $get('status') === 'ditolak'),
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
                Tables\Columns\TextColumn::make('employee.units.name')
                    ->label('Unit Kerja')
                    ->badge(),
                Tables\Columns\TextColumn::make('employee.jabatan')
                    ->label('Jabatan')
                    ->badge(),
                Tables\Columns\TextColumn::make('tanggal_resign')
                    ->date()
                    ->label('Tanggal Resign'),
                Tables\Columns\TextColumn::make('alasan')
                    ->label('Alasan Resign')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('upload_file')
                    ->label('Bukti')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat/Download' : '-')
                    ->url(fn($record) => $record->upload_file ? asset('storage/' . $record->upload_file) : null, true)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color(fn($record) => $record->upload_file ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('keterangan_tindak_lanjut')
                    ->label('Catatan')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Ubah'),

                            $user = Auth::user();
                            return $user?->hasAnyRole(['super_admin', 'admin', 'admin_unit'])
                                && $record->status === 'diajukan';
                        })
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'disetujui',
                            ]);
                            // UPDATE DATA INDUK
                            $record->employee->update([
                                'status' => 'Resign',
                                'keterangan' => $record->alasan,
                            ]);
                        }),

                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->schema([
                            Forms\Components\Textarea::make('keterangan_tindak_lanjut')
                                ->label('Alasan Penolakan')
                                ->required(),
                        ])
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return $user?->hasAnyRole(['super_admin', 'admin', 'admin_unit'])
                                && $record->status === 'diajukan';
                        })
                        ->action(
                            fn($record, array $data) =>
                            $record->update([
                                'status' => 'ditolak',
                                'keterangan_tindak_lanjut' => $data['keterangan_tindak_lanjut'],
                            ])
                        ),
                ]),
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
            'index' => Pages\ListResigns::route('/'),
            'create' => Pages\CreateResign::route('/create'),
            'edit' => Pages\EditResign::route('/{record}/edit'),
        ];
    }
}
