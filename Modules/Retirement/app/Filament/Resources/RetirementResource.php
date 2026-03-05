<?php

namespace Modules\Retirement\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Modules\Retirement\Filament\Resources\RetirementResource\Pages;
use Modules\Retirement\Models\Retirement;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RetirementResource extends Resource
{
    protected static ?string $model = Retirement::class;
    protected static ?int $navigationSort = 11;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-document-check';
    }

    public static function getActiveNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-s-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan Pensiun';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan Pensiun';
    }

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

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pengajuan pensiun menunggu persetujuan';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['employee.units']);
        /** @var \App\Models\User $user */
        $user = Auth::user();

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

        if ($user->hasRole('staff')) {
            return $query->whereHas('employee', fn($q) => $q->where('user_id', $user->id));
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Pengajuan Pensiun')
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

                        Forms\Components\DatePicker::make('tanggal_pensiun')
                            ->required()
                            ->label('Tanggal Pensiun')
                            ->helperText('Tanggal efektif memulai pensiun'),

                        Forms\Components\Toggle::make('is_khidmah')
                            ->label('Lanjut Khidmah (Purna Tugas)')
                            ->helperText('Aktifkan jika pegawai tetap mengabdi setelah pensiun')
                            ->default(false),

                        Forms\Components\Textarea::make('alasan')
                            ->label('Keterangan / Alasan Pensiun')
                            ->placeholder('Contoh: Batas Usia Pensiun (BUP) atau Pensiun Dini')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('status')
                            ->default('diajukan')
                            ->dehydrated(true)
                            ->visible(function () {
                                /** @var \App\Models\User $user */
                                $user = Auth::user();
                                return ! $user?->hasAnyRole(['super_admin', 'admin', 'ketua_psdm']);
                            }),

                        Forms\Components\FileUpload::make('upload_file')
                            ->label('Dokumen Pendukung (PDF/JPG/PNG)')
                            ->directory('pensiun')
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
                        return $user?->hasAnyRole(['super_admin', 'admin', 'ketua_psdm']);
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
                Tables\Columns\TextColumn::make('tanggal_pensiun')
                    ->date()
                    ->label('Tanggal Pensiun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('upload_file')
                    ->label('Dokumen')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat' : '-')
                    ->url(fn($record) => $record->upload_file ? asset('storage/' . $record->upload_file) : null, true)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color(fn($record) => $record->upload_file ? 'info' : 'gray'),
                Tables\Columns\IconColumn::make('is_khidmah')
                    ->label('Khidmah')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'violet' : 'gray'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_khidmah')
                    ->label('Lanjut Khidmah?'),
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
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if ($user?->hasRole('super_admin')) return true;
                            return $user?->hasRole('staff')
                                && $record->data_induk_id === $user->employee?->id
                                && $record->status === 'diajukan';
                        }),
                    Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(function ($record) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            return $user?->hasAnyRole(['super_admin', 'admin', 'ketua_psdm'])
                                && $record->status === 'diajukan';
                        })
                        ->action(function ($record) {
                            $record->update(['status' => 'disetujui']);

                            $employee = $record->employee;
                            if ($employee) {
                                if ($record->is_khidmah) {
                                    $employee->update([
                                        'status' => 'Aktif',
                                        'status_kepegawaian' => 'Purna Tugas (Khidmah)',
                                    ]);
                                } else {
                                    $employee->update([
                                        'status' => 'Pensiun',
                                    ]);
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Pensiun Disetujui')
                                ->success()
                                ->send();
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
                            return $user?->hasAnyRole(['super_admin', 'admin', 'ketua_psdm'])
                                && $record->status === 'diajukan';
                        })
                        ->action(
                            fn($record, array $data) =>
                            $record->update([
                                'status' => 'ditolak',
                                'keterangan_tindak_lanjut' => $data['keterangan_tindak_lanjut'],
                            ])
                        ),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRetirements::route('/'),
            'create' => Pages\CreateRetirement::route('/create'),
            'edit' => Pages\EditRetirement::route('/{record}/edit'),
        ];
    }
}
