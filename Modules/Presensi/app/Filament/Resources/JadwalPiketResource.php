<?php

namespace Modules\Presensi\Filament\Resources;

use Modules\Presensi\Filament\Resources\JadwalPiketResource\Pages;
use Modules\Presensi\Models\JadwalPiket;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Actions\BulkActionGroup;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class JadwalPiketResource extends Resource
{
    protected static ?string $model = JadwalPiket::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | \UnitEnum | null $navigationGroup = 'Presensi';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Jadwal Piket';

    protected static ?string $modelLabel = 'Jadwal Piket';

    protected static ?string $pluralModelLabel = 'Jadwal Piket';

    public static function getNavigationBadge(): ?string
    {
        $count = JadwalPiket::getTodayPiketCount();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->hasAnyRole(['super_admin', 'admin_unit', 'ketua_psdm']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Jadwal Piket')
                    ->schema([
                        Select::make('user_id')
                            ->label('Petugas Piket')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn(User $record) => "{$record->name} - {$record->email}")
                            ->columnSpanFull(),

                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Select::make('shift')
                            ->label('Shift')
                            ->options([
                                'pagi' => 'Pagi',
                                'siang' => 'Siang',
                                'sore' => 'Sore',
                            ])
                            ->nullable()
                            ->placeholder('Pilih shift (opsional)')
                            ->columnSpan(1),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (opsional)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Petugas')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->tanggal->isToday() ? 'success' : 'gray')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->tanggal->isToday()
                            ? 'Hari Ini - ' . $record->tanggal->format('d M Y')
                            : $record->tanggal->format('d M Y')
                    ),

                TextColumn::make('shift')
                    ->label('Shift')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pagi' => 'warning',
                        'siang' => 'info',
                        'sore' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state ? ucfirst($state) : '-'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('today')
                    ->label('Piket Hari Ini')
                    ->query(fn(Builder $query) => $query->today())
                    ->toggle()
                    ->default(true),

                Filter::make('upcoming')
                    ->label('Jadwal Mendatang')
                    ->query(fn(Builder $query) => $query->active())
                    ->toggle(),

                SelectFilter::make('user_id')
                    ->label('Petugas')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('shift')
                    ->label('Shift')
                    ->options([
                        'pagi' => 'Pagi',
                        'siang' => 'Siang',
                        'sore' => 'Sore',
                    ]),

                Filter::make('tanggal')
                    ->schema([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalPikets::route('/'),
            'create' => Pages\CreateJadwalPiket::route('/create'),
            'edit' => Pages\EditJadwalPiket::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->hasAnyRole(['super_admin', 'admin_unit', 'ketua_psdm']) ?? false;
    }
}
