<?php

namespace Modules\Inventory\Filament\Resources;

use Modules\Inventory\Models\StockTransaction;
use Modules\Inventory\Models\Barang;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Filament\Resources\StockTransactionResource\Pages;

class StockTransactionResource extends Resource
{
    protected static ?string $model = StockTransaction::class;
    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-arrow-path';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventaris';
    }

    public static function getModelLabel(): string
    {
        return 'Mutasi Stok';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mutasi Stok';
    }

    /**
     * Unit-Based Access:
     * - super_admin / ketua_psdm : lihat semua mutasi
     * - admin_unit / koor_jenjang / kepala_sekolah : bisa tambah & lihat semua (pengelola gudang)
     * - staff : tidak boleh akses mutasi stok
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['barang', 'createdBy']);
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']);
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasAnyRole(['super_admin', 'admin_unit']);
    }

    public static function canDelete($record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('barang_id')
                ->label('Barang')
                ->options(fn() => Barang::where('is_active', true)->pluck('nama_barang', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\Select::make('type')
                ->label('Jenis Mutasi')
                ->options([
                    'in'     => 'Masuk (Pengadaan)',
                    'out'    => 'Keluar (Pemakaian)',
                    'opname' => 'Opname / Koreksi',
                ])
                ->required()
                ->native(false),

            Forms\Components\TextInput::make('quantity')
                ->label('Jumlah')
                ->numeric()
                ->required()
                ->minValue(1),

            Forms\Components\Textarea::make('remarks')
                ->label('Keterangan')
                ->columnSpanFull(),

            Forms\Components\Hidden::make('created_by')
                ->default(fn() => Auth::id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('barang.nama_barang')
                    ->label('Barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'in'     => 'Masuk',
                        'out'    => 'Keluar',
                        'opname' => 'Opname',
                        default  => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'in'     => 'success',
                        'out'    => 'danger',
                        'opname' => 'warning',
                        default  => 'gray',
                    }),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stok_sebelum_transaksi')
                    ->label('Stok Sblm')
                    ->numeric()
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('stok_setelah_transaksi')
                    ->label('Stok Ssdh')
                    ->numeric()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Oleh')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remarks')
                    ->label('Keterangan')
                    ->limit(40),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'in'     => 'Masuk',
                        'out'    => 'Keluar',
                        'opname' => 'Opname',
                    ]),
                Tables\Filters\SelectFilter::make('barang_id')
                    ->label('Barang')
                    ->relationship('barang', 'nama_barang'),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockTransactions::route('/'),
            'create' => Pages\CreateStockTransaction::route('/create'),
        ];
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
