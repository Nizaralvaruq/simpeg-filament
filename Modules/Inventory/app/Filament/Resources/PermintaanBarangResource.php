<?php

namespace Modules\Inventory\Filament\Resources;

use Modules\Inventory\Models\PermintaanBarang;
use Modules\Inventory\Models\PermintaanBarangDetail;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\StockTransaction;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']);

        return $schema->components([
            \Filament\Schemas\Components\Section::make('Informasi Permintaan')
                ->schema([
                    Forms\Components\TextInput::make('nomor_permintaan')
                        ->label('Nomor Permintaan')
                        ->disabled()
                        ->placeholder('Otomatis dibuat saat disimpan')
                        ->columnSpan(2),

                    Forms\Components\Hidden::make('user_id')
                        ->default(fn() => Auth::id()),

                    Forms\Components\Select::make('unit_id')
                        ->label('Unit / Jenjang')
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
                        ->native(false)
                        ->disabled(!$isAdmin),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan / Keperluan')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('alasan_penolakan')
                        ->label('Alasan Penolakan')
                        ->columnSpanFull()
                        ->visible(fn($record) => $record?->status === 'ditolak')
                        ->disabled(!$isAdmin),
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
                                ->minValue(0)
                                ->visible($isAdmin),

                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->columnSpanFull(),
                        ])
                        ->columns($isAdmin ? 3 : 2)
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
                Tables\Columns\TextColumn::make('nomor_permintaan')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit / Jenjang')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('tanggal_permintaan')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('details_count')
                    ->label('Jml Item')
                    ->counts('details')
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
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'draft'     => 'Draft',
                        'diajukan'  => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                        'selesai'   => 'Selesai',
                        default     => $state,
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
                    ->label('Unit / Jenjang')
                    ->relationship('unit', 'name'),
            ])
            ->recordActions([
                Action::make('ajukan')
                    ->label('Ajukan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Permintaan?')
                    ->modalDescription('Permintaan akan dikirim ke admin untuk diproses. Pastikan data sudah benar.')
                    ->visible(fn($record) => $record->status === 'draft' && Auth::id() === $record->user_id)
                    ->action(function ($record) {
                        $record->update(['status' => 'diajukan']);

                        // Notifikasi ke admin unit
                        Notification::make()
                            ->title('Permintaan Barang Baru')
                            ->body("Permintaan {$record->nomor_permintaan} dari {$record->user->name} telah diajukan.")
                            ->success()
                            ->sendToDatabase(
                                \App\Models\User::role(['super_admin', 'admin_unit'])->get()
                            );

                        Notification::make()
                            ->title('Permintaan berhasil diajukan')
                            ->success()
                            ->send();
                    }),

                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Permintaan?')
                    ->modalDescription('Barang belum langsung dikurangi. Admin perlu klik "Selesaikan" setelah barang diserahkan.')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'diajukan'
                            && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']);
                    })
                    ->action(function ($record) {
                        $record->update([
                            'status'      => 'disetujui',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);

                        // Notifikasi ke pemohon
                        Notification::make()
                            ->title('Permintaan Disetujui')
                            ->body("Permintaan {$record->nomor_permintaan} Anda telah disetujui.")
                            ->success()
                            ->sendToDatabase($record->user);

                        Notification::make()
                            ->title('Permintaan berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Permintaan')
                    ->schema([
                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'diajukan'
                            && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']);
                    })
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'           => 'ditolak',
                            'alasan_penolakan' => $data['alasan_penolakan'],
                        ]);

                        // Notifikasi ke pemohon
                        Notification::make()
                            ->title('Permintaan Ditolak')
                            ->body("Permintaan {$record->nomor_permintaan} ditolak. Alasan: {$data['alasan_penolakan']}")
                            ->danger()
                            ->sendToDatabase($record->user);

                        Notification::make()
                            ->title('Permintaan berhasil ditolak')
                            ->success()
                            ->send();
                    }),

                Action::make('selesaikan')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Selesaikan Permintaan?')
                    ->modalDescription('Stok barang akan dikurangi sesuai jumlah yang disetujui. Pastikan barang sudah diserahkan.')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'disetujui'
                            && $user->hasAnyRole(['super_admin', 'admin_unit']);
                    })
                    ->action(function ($record) {
                        // Validasi stok mencukupi untuk semua item
                        $errors = [];
                        foreach ($record->details as $detail) {
                            $barang = Barang::find($detail->barang_id);
                            $jumlah = $detail->jumlah_disetujui ?? $detail->jumlah_diminta;
                            if ($barang && $barang->stok_saat_ini < $jumlah) {
                                $errors[] = "Stok {$barang->nama_barang} tidak mencukupi (tersedia: {$barang->stok_saat_ini}, dibutuhkan: {$jumlah})";
                            }
                        }

                        if (!empty($errors)) {
                            Notification::make()
                                ->title('Stok tidak mencukupi')
                                ->body(implode("\n", $errors))
                                ->danger()
                                ->send();
                            return;
                        }

                        DB::transaction(function () use ($record) {
                            foreach ($record->details as $detail) {
                                $barang = Barang::find($detail->barang_id);
                                $jumlah = $detail->jumlah_disetujui ?? $detail->jumlah_diminta;

                                if ($barang && $jumlah > 0) {
                                    // Buat stock transaction
                                    StockTransaction::create([
                                        'barang_id'      => $barang->id,
                                        'type'           => 'out',
                                        'quantity'       => $jumlah,
                                        'reference_type' => 'PermintaanBarang',
                                        'reference_id'   => $record->id,
                                        'remarks'        => "Pengeluaran untuk permintaan {$record->nomor_permintaan} — Unit: {$record->unit?->name}",
                                        'created_by'     => Auth::id(),
                                    ]);

                                    // Kurangi stok
                                    $barang->decrement('stok_saat_ini', $jumlah);
                                }
                            }

                            $record->update(['status' => 'selesai']);
                        });

                        // Notifikasi ke pemohon
                        Notification::make()
                            ->title('Permintaan Selesai')
                            ->body("Permintaan {$record->nomor_permintaan} telah diselesaikan. Barang siap diambil.")
                            ->success()
                            ->sendToDatabase($record->user);

                        Notification::make()
                            ->title('Permintaan berhasil diselesaikan')
                            ->success()
                            ->send();
                    }),

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
            'index'  => Pages\ListPermintaanBarangs::route('/'),
            'create' => Pages\CreatePermintaanBarang::route('/create'),
            'edit'   => Pages\EditPermintaanBarang::route('/{record}/edit'),
            'view'   => Pages\ViewPermintaanBarang::route('/{record}'),
        ];
    }
}
