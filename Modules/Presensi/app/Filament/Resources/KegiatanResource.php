<?php

namespace Modules\Presensi\Filament\Resources;

use Modules\Presensi\Filament\Resources\KegiatanResource\Pages;
use Modules\Presensi\Models\Kegiatan;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Presensi';
    }

    public static function getModelLabel(): string
    {
        return 'Kegiatan';
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var User $user */
        $user = Auth::user();

        // Admin: Show Total Activities Today
        if ($user && $user->hasRole('super_admin')) {
            return (string) static::getModel()::whereDate('tanggal', now())->count();
        }

        // Staff: Show "Remaining" Activities (Unattended)
        return (string) static::getModel()::whereDate('tanggal', now())
            ->whereDoesntHave('absensiKegiatans', fn($q) => $q->where('user_id', Auth::id()))
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return (int) self::getNavigationBadge() > 0 ? 'success' : 'gray';
    }

    public static function infolist(Schema $schema): Schema
    {
        // Custom infolist for View page - returning empty to hide "Informasi Kegiatan" section
        // as requested by user to focus on Relation Manager (Laporan Kehadiran)
        return $schema->components([]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kegiatan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kegiatan')
                            ->required()
                            ->label('Nama Kegiatan'),
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now())
                            ->label('Tanggal'),
                        Forms\Components\TimePicker::make('jam_mulai')
                            ->required()
                            ->label('Jam Mulai'),
                        Forms\Components\TimePicker::make('jam_selesai')
                            ->required()
                            ->label('Jam Selesai'),
                        Forms\Components\TextInput::make('lokasi')
                            ->required()
                            ->label('Lokasi'),
                        Forms\Components\Toggle::make('is_wajib')
                            ->label('Kegiatan Wajib')
                            ->default(false),
                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull()
                            ->label('Keterangan'),
                    ])->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Staff sees: (1) activities from today onwards OR (2) past activities they attended
        return $query->where(function ($q) use ($user) {
            $q->where('tanggal', '>=', now()->toDateString())
                ->orWhereHas('absensiKegiatans', fn($subQ) => $subQ->where('user_id', $user->id));
        });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->time(),
                Tables\Columns\TextColumn::make('jam_selesai')
                    ->time(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_wajib')
                    ->boolean()
                    ->label('Wajib'),
                Tables\Columns\TextColumn::make('absensiKegiatans.status')
                    ->label('Status Absensi')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir' => 'danger',
                        default => 'gray',
                    })
                    ->getStateUsing(function (Kegiatan $record) {
                        $absensi = $record->absensiKegiatans()->where('user_id', Auth::id())->first();
                        return $absensi?->status ?? 'Belum Absen';
                    })
                    ->visible(function () {
                        /** @var User $user */
                        $user = Auth::user();
                        return $user instanceof User && !$user->hasRole('super_admin');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('absen_hadir')
                    ->label('Absen Hadir')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(function (Kegiatan $record) {
                        /** @var User $user */
                        $user = Auth::user();
                        // Super Admin cannot attend
                        if ($user instanceof User && $user->hasRole('super_admin')) {
                            return false;
                        }

                        // Check if already absented
                        $exists = $record->absensiKegiatans()->where('user_id', Auth::id())->exists();
                        if ($exists) return false;

                        // Time restriction: Only show during activity hours
                        $now = now();
                        $activityDate = $record->tanggal->format('Y-m-d');
                        $startTime = \Carbon\Carbon::parse($activityDate . ' ' . $record->jam_mulai);
                        $endTime = \Carbon\Carbon::parse($activityDate . ' ' . $record->jam_selesai);

                        // Debug: Log waktu untuk debugging (hapus setelah testing)
                        Log::info('Activity Time Check', [
                            'now' => $now->format('Y-m-d H:i:s'),
                            'start' => $startTime->format('Y-m-d H:i:s'),
                            'end' => $endTime->format('Y-m-d H:i:s'),
                            'between' => $now->between($startTime, $endTime)
                        ]);

                        return $now->between($startTime, $endTime);
                    })
                    ->action(function (Kegiatan $record) {
                        $record->absensiKegiatans()->create([
                            'user_id' => Auth::id(),
                            'jam_absen' => now(),
                            'status' => 'hadir',
                        ]);
                    })
                    ->requiresConfirmation(),
                Action::make('absen_tidak_hadir')
                    ->label('Absen Tidak Hadir')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(function (Kegiatan $record) {
                        /** @var User $user */
                        $user = Auth::user();
                        // Super Admin cannot attend
                        if ($user instanceof User && $user->hasRole('super_admin')) {
                            return false;
                        }

                        // Check if already absented
                        $exists = $record->absensiKegiatans()->where('user_id', Auth::id())->exists();
                        if ($exists) return false;

                        // Time restriction: Only show during activity hours
                        $now = now();
                        $activityDate = $record->tanggal->format('Y-m-d');
                        $startTime = \Carbon\Carbon::parse($activityDate . ' ' . $record->jam_mulai);
                        $endTime = \Carbon\Carbon::parse($activityDate . ' ' . $record->jam_selesai);

                        return $now->between($startTime, $endTime);
                    })
                    ->form([
                        Forms\Components\Textarea::make('keterangan')
                            ->required()
                            ->label('Alasan Tidak Hadir'),
                    ])
                    ->action(function (Kegiatan $record, array $data) {
                        $record->absensiKegiatans()->create([
                            'user_id' => Auth::id(),
                            'jam_absen' => now(),
                            'status' => 'tidak_hadir',
                            'keterangan' => $data['keterangan'],
                        ]);
                    }),
                ViewAction::make()
                    ->label('Laporan')
                    ->visible(function () {
                        /** @var User $user */
                        $user = Auth::user();
                        return $user instanceof User && $user->hasRole('super_admin');
                    }),
                EditAction::make()
                    ->visible(function () {
                        /** @var User $user */
                        $user = Auth::user();
                        return $user instanceof User && $user->hasRole('super_admin');
                    }),
                DeleteAction::make()
                    ->visible(function () {
                        /** @var User $user */
                        $user = Auth::user();
                        return $user instanceof User && $user->hasRole('super_admin');
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(function () {
                    /** @var User $user */
                    $user = Auth::user();
                    return $user instanceof User && $user->hasRole('super_admin');
                }),
            ]);
    }

    public static function getRelations(): array
    {
        /** @var User $user */
        $user = Auth::user();

        // Only Super Admin can see the Attendance Report (Relation Manager)
        if ($user instanceof User && $user->hasRole('super_admin')) {
            return [
                KegiatanResource\RelationManagers\AbsensiKegiatansRelationManager::class,
            ];
        }

        return [];
    }

    public static function getRecordUrl(\Illuminate\Database\Eloquent\Model $record): ?string
    {
        // Redirect to view page instead of edit when clicking record
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatans::route('/'),
            'create' => Pages\CreateKegiatan::route('/create'),
            'view' => Pages\ViewKegiatan::route('/{record}'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
        ];
    }
}
