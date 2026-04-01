<?php

namespace Modules\Inventory\Filament\Resources;

use Modules\Inventory\Models\PermintaanBarang;
use Modules\Inventory\Models\Barang;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Filament\Resources\PermintaanBarangResource\Pages;

class PermintaanBarangResource extends Resource
{
    protected static ?string $model = PermintaanBarang::class;
    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventaris';
    }

    public static function getModelLabel(): string
    {
        return 'Permintaan Barang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Permintaan Barang';
    }

    /**
     * Unit-Based Access:
     * - super_admin / ketua_psdm : semua permintaan
     * - admin_unit / koor_jenjang / kepala_sekolah : hanya permintaan dari unitnya
     * - staff : hanya permintaan miliknya sendiri
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return $query;
        }

        if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
            $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
            if (empty($unitIds)) {
                return $query->whereRaw('1=0');
            }
            return $query->whereIn('unit_id', $unitIds);
        }

        if ($user->hasRole('staff')) {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Informasi Permintaan')
                ->schema([
                    Forms\Components\Hidden::make('user_id')
                        ->default(fn() => Auth::id()),

                    Forms\Components\Select::make('unit_id')
                        ->label('Unit / Satuan Kerja')
                        ->relationship('unit', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal_permintaan')
                        ->label('Tanggal Permintaan')
                        ->default(now())
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft'      => 'Draft',
                            'diajukan'   => 'Diajukan',
                            'disetujui'  => 'Disetujui',
                            'ditolak'    => 'Ditolak',
                            'selesai'    => 'Selesai',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan')
                        ->columnSpanFull(),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Detail Barang yang Diminta')
                ->schema([
                    Forms\Components\Repeater::make('details')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Forms\Components\Select::make('barang_id')
                                ->label('Barang')
                                ->options(fn() => Barang::where('is_active', true)->pluck('nama_barang', 'id'))
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('jumlah_diminta')
                                ->label('Jumlah Diminta')
                                ->numeric()
                                ->required()
                                ->minValue(1),

                            Forms\Components\TextInput::make('jumlah_disetujui')
                                ->label('Jumlah Disetujui')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),

                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->defaultItems(1)
                        ->addActionLabel('Tambah Barang')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_permintaan')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'draft'     => 'gray',
                        'diajukan'  => 'warning',
                        'disetujui' => 'success',
                        'ditolak'   => 'danger',
                        'selesai'   => 'primary',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'      => 'Draft',
                        'diajukan'   => 'Diajukan',
                        'disetujui'  => 'Disetujui',
                        'ditolak'    => 'Ditolak',
                        'selesai'    => 'Selesai',
                    ]),
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\ViewAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_permintaan', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermintaanBarangs::route('/'),
            'create' => Pages\CreatePermintaanBarang::route('/create'),
            'edit' => Pages\EditPermintaanBarang::route('/{record}/edit'),
        ];
    }
}
