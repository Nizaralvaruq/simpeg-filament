<?php

namespace Modules\Inventory\Filament\Resources;

use Modules\Inventory\Models\Peminjaman;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\StockTransaction;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Filament\Resources\PeminjamanResource\Pages;
use Filament\Notifications\Notification;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-hand-raised';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventaris';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getModelLabel(): string
    {
        return 'Peminjaman';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Peminjaman Barang/Ruangan';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Staff hanya lihat peminjamannya sendiri
        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah', 'koor_jenjang', 'admin_unit'])) {
            return $query->where('user_id', $user->id);
        }

        // Admin lihat sesuai unitnya
        if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
            $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
            return $query->whereIn('unit_id', $unitIds);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Info Peminjaman')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_peminjaman')
                            ->label('Nomor Peminjaman')
                            ->disabled()
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id()),

                        Forms\Components\Select::make('unit_id')
                            ->label('Kaitkan ke Unit (Opsional)')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::user()->employee?->units->first()?->id)
                            ->disabled(fn ($record) => $record && $record->status !== 'draft'),

                        Forms\Components\DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->default(now())
                            ->disabled(fn ($record) => $record && $record->status !== 'draft'),

                        Forms\Components\DatePicker::make('rencana_kembali')
                            ->label('Rencana Pengembalian')
                            ->required()
                            ->default(now()->addDays(1))
                            ->disabled(fn ($record) => $record && $record->status !== 'draft'),

                        Forms\Components\Textarea::make('keperluan')
                            ->label('Keperluan')
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record && $record->status !== 'draft'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Barang / Ruangan yang Dipinjam')
                    ->schema([
                        Forms\Components\Repeater::make('details')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('barang_id')
                                    ->label('Barang / Ruangan')
                                    ->options(function () {
                                        return Barang::where('is_active', true)
                                            ->where('stok_saat_ini', '>', 0)
                                            ->get()
                                            ->mapWithKeys(function ($barang) {
                                                $nama = $barang->nama_barang . ' (Stok: ' . $barang->stok_saat_ini . ')';
                                                return [$barang->id => $nama];
                                            });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                Forms\Components\TextInput::make('jumlah_pinjam')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $barangId = $get('barang_id');
                                        if ($barangId) {
                                            $barang = Barang::find($barangId);
                                            if ($barang && $state > $barang->stok_saat_ini) {
                                                Notification::make()
                                                    ->title('Stok Kurang')
                                                    ->body("Stok {$barang->nama_barang} hanya {$barang->stok_saat_ini}")
                                                    ->danger()
                                                    ->send();
                                                $set('jumlah_pinjam', $barang->stok_saat_ini);
                                            }
                                        }
                                    }),
                            ])
                            ->columns(2)
                            ->disabled(fn ($record) => $record && $record->status !== 'draft')
                            ->minItems(1)
                            ->addActionLabel('Tambah Barang/Ruangan'),
                    ]),

                \Filament\Schemas\Components\Section::make('Informasi Pengembalian')
                    ->visible(fn ($record) => $record && in_array($record->status, ['menunggu_pengecekan', 'dikembalikan_baik', 'dikembalikan_rusak']))
                    ->schema([
                        Forms\Components\Textarea::make('catatan_pengembalian')
                            ->label('Catatan dari Peminjam')
                            ->disabled()
                            ->columnSpanFull(),
                            
                        Forms\Components\Repeater::make('details_kembali')
                            ->relationship('details')
                            ->label('Cek Fisik Pengembalian')
                            ->schema([
                                Forms\Components\Select::make('barang_id')
                                    ->label('Barang')
                                    ->options(fn() => Barang::pluck('nama_barang', 'id'))
                                    ->disabled(),
                                Forms\Components\TextInput::make('jumlah_pinjam')
                                    ->label('Jumlah Dipinjam')
                                    ->disabled(),
                                Forms\Components\Textarea::make('kondisi_sesudah')
                                    ->label('Kondisi Fisik Saat Dikembalikan')
                                    ->visible(fn() => Auth::user()->hasAnyRole(['super_admin', 'admin_unit']))
                                    ->disabled(fn ($record) => $record && $record->peminjaman->status !== 'menunggu_pengecekan'),
                            ])
                            ->columns(3)
                            ->disableItemCreation()
                            ->disableItemDeletion(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_peminjaman')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('rencana_kembali')
                    ->date('d M Y')
                    ->color(fn ($record) => now()->startOfDay()->gt($record->rencana_kembali) && in_array($record->status, ['dipinjam']) ? 'danger' : null),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'draft' => 'gray',
                        'diajukan' => 'warning',
                        'dipinjam' => 'info',
                        'menunggu_pengecekan' => 'warning',
                        'dikembalikan_baik' => 'success',
                        'dikembalikan_rusak' => 'danger',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => strtoupper(str_replace('_', ' ', $state))),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamens::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'view' => Pages\ViewPeminjaman::route('/{record}'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}
