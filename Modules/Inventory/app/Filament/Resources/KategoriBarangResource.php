<?php

namespace Modules\Inventory\Filament\Resources;

use Modules\Inventory\Models\KategoriBarang;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Filament\Resources\KategoriBarangResource\Pages;

class KategoriBarangResource extends Resource
{
    protected static ?string $model = KategoriBarang::class;
    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-tag';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventaris';
    }

    public static function getModelLabel(): string
    {
        return 'Kategori Barang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kategori Barang';
    }

    /**
     * Kategori Barang adalah data master.
     * Hanya Super Admin dan Admin Unit yang boleh mengelola.
     */
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
            \Filament\Schemas\Components\Section::make('Informasi Kategori')
                ->schema([
                    Forms\Components\TextInput::make('nama_kategori')
                        ->label('Nama Kategori')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),

                Tables\Columns\TextColumn::make('barangs_count')
                    ->label('Jumlah Barang')
                    ->counts('barangs')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            'index' => Pages\ListKategoriBarangs::route('/'),
            'create' => Pages\CreateKategoriBarang::route('/create'),
            'edit' => Pages\EditKategoriBarang::route('/{record}/edit'),
        ];
    }
}
