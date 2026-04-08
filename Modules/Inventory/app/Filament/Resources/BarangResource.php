<?php

namespace Modules\Inventory\Filament\Resources;

use Modules\Inventory\Models\Barang;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Filament\Resources\BarangResource\Pages;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;
    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-archive-box';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventaris';
    }

    public static function getModelLabel(): string
    {
        return 'Barang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Barang';
    }

    /**
     * Barang adalah data master global.
     * Super Admin: kelola penuh. Admin Unit & Staff: hanya lihat.
     */
    public static function getEloquentQuery(): Builder
    {
        // Barang berfungsi sebagai katalog global, semua user (termasuk Staff) 
        // diizinkan melihat semua barang agar mudah saat ingin meminjam.
        return parent::getEloquentQuery();
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasAnyRole(['super_admin', 'admin_unit']);
    }

    public static function canEdit($record): bool
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
            \Filament\Schemas\Components\Section::make('Informasi Barang')
                ->schema([
                    Forms\Components\Select::make('kategori_id')
                        ->label('Kategori')
                        ->relationship('kategori', 'nama_kategori')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('unit_id')
                        ->label('Pemilik / Pengelola Unit')
                        ->relationship('unit', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Pusat / Global (Semua Unit)')
                        ->helperText('Pilih unit jika barang khusus dimiliki sekolah/jenjang tertentu.'),

                    Forms\Components\TextInput::make('kode_barang')
                        ->label('Kode Barang')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('nama_barang')
                        ->label('Nama Barang')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('jenis')
                        ->label('Jenis Barang')
                        ->options([
                            'Aset' => 'Aset (Barang Inventaris)',
                            'BHP'  => 'BHP (Bahan Habis Pakai)',
                        ])
                        ->default('Aset')
                        ->required()
                        ->native(false),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Detail & Lokasi')
                ->schema([
                    Forms\Components\Textarea::make('spesifikasi')
                        ->label('Spesifikasi')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('lokasi_ruangan')
                        ->label('Lokasi / Ruangan')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('minimum_stok')
                        ->label('Minimum Stok')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->minValue(0),

                    Forms\Components\TextInput::make('stok_saat_ini')
                        ->label('Stok Saat Ini (Awal)')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->minValue(0)
                        ->hiddenOn('edit')
                        ->helperText('Gunakan menu Mutasi Stok untuk menambah stok barang lama.'),

                    Forms\Components\FileUpload::make('foto')
                        ->label('Foto Barang')
                        ->image()
                        ->disk('public')
                        ->directory('inventaris/foto')
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),

                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('kode_barang')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Kepemilikan Unit')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->default('Global'),

                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Aset' => 'primary',
                        'BHP'  => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('stok_saat_ini')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->color(fn($record) => $record->stok_saat_ini <= $record->minimum_stok ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('lokasi_ruangan')
                    ->label('Lokasi')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori'),

                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Filter Unit')
                    ->relationship('unit', 'name')
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $user?->hasAnyRole(['super_admin', 'ketua_psdm']);
                    }),

                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'Aset' => 'Aset',
                        'BHP'  => 'BHP',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\ViewAction::make(),
            ])
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
            'index'  => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit'   => Pages\EditBarang::route('/{record}/edit'),
            'view'   => Pages\ViewBarang::route('/{record}'),
        ];
    }
}
