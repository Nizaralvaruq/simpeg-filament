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
        /** @var \App\Models\User $user */
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
                                            ->get()
                                            ->mapWithKeys(function ($barang) {
                                                $nama = $barang->nama_barang . ' (Stok: ' . $barang->stok_saat_ini . ')';
                                                return [$barang->id => $nama];
                                            });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->disabled(fn ($get) => $get('../../status') && $get('../../status') !== 'draft'),

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
                                    })
                                    ->disabled(fn ($get) => $get('../../status') && $get('../../status') !== 'draft'),

                                Forms\Components\Textarea::make('kondisi_sesudah')
                                    ->label('Kondisi Fisik Saat Dikembalikan')
                                    ->visible(fn ($get) => in_array($get('../../status'), ['menunggu_pengecekan', 'dikembalikan_baik', 'dikembalikan_rusak']))
                                    ->disabled(function ($get) {
                                        /** @var \App\Models\User $user */
                                        $user = Auth::user();
                                        return $get('../../status') !== 'menunggu_pengecekan' || !$user->hasAnyRole(['super_admin', 'admin_unit']);
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addable(fn ($record) => !$record || $record->status === 'draft')
                            ->deletable(fn ($record) => !$record || $record->status === 'draft')
                            ->reorderable(fn ($record) => !$record || $record->status === 'draft')
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

                Tables\Columns\TextColumn::make('details.barang.nama_barang')
                    ->label('Barang/Ruangan')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->searchable(),

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
                \Filament\Actions\Action::make('setujui')
                    ->label('Setujui')
                    ->color('info')
                    ->icon('heroicon-o-check-circle')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'diajukan' && $user->hasAnyRole(['super_admin', 'admin_unit']);
                    })
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        foreach ($record->details as $detail) {
                            $barang = Barang::find($detail->barang_id);
                            if ($barang->stok_saat_ini < $detail->jumlah_pinjam) {
                                Notification::make()->title("Stok {$barang->nama_barang} tidak mencukupi!")->danger()->send();
                                return;
                            }
                        }

                        foreach ($record->details as $detail) {
                            $barang = Barang::find($detail->barang_id);
                            
                            StockTransaction::create([
                                'barang_id' => $barang->id,
                                'type' => 'out',
                                'quantity' => $detail->jumlah_pinjam,
                                'stok_sebelum_transaksi' => $barang->stok_saat_ini,
                                'stok_setelah_transaksi' => $barang->stok_saat_ini - $detail->jumlah_pinjam,
                                'remarks' => "Dipinjam: " . $record->nomor_peminjaman,
                                'created_by' => Auth::id(),
                            ]);

                            $barang->decrement('stok_saat_ini', $detail->jumlah_pinjam);
                        }

                        $record->update([
                            'status' => 'dipinjam',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);

                        Notification::make()->title('Pemesanan disetujui, Stok telah diperbarui.')->success()->send();
                    }),

                \Filament\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'diajukan' && $user->hasAnyRole(['super_admin', 'admin_unit']);
                    })
                    ->form([
                        \Filament\Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'status' => 'ditolak',
                            'alasan_penolakan' => $data['alasan_penolakan'],
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Peminjaman ditolak')->success()->send();
                    }),

                \Filament\Actions\Action::make('ajukan_pengembalian')
                    ->label('Ajukan Pengembalian')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'dipinjam' && $record->user_id === $user->id;
                    })
                    ->form([
                        \Filament\Forms\Components\Textarea::make('catatan_pengembalian')
                            ->label('Catatan Kondisi Barang (Opsional)')
                            ->placeholder('Misal: Baterai proyektor sudah lemah..')
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'status' => 'menunggu_pengecekan',
                            'catatan_pengembalian' => $data['catatan_pengembalian'] ?? null,
                        ]);
                        Notification::make()->title('Pengembalian diajukan, menunggu pengecekan Admin.')->success()->send();
                    }),

                \Filament\Actions\Action::make('terima_baik')
                    ->label('Terima (Baik)')
                    ->color('success')
                    ->icon('heroicon-o-check-badge')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'menunggu_pengecekan' && $user->hasAnyRole(['super_admin', 'admin_unit']);
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Stok ini akan dikembalikan ke inventaris.')
                    ->action(function ($record) {
                        foreach ($record->details as $detail) {
                            $barang = Barang::find($detail->barang_id);
                            
                            StockTransaction::create([
                                'barang_id' => $barang->id,
                                'type' => 'in',
                                'quantity' => $detail->jumlah_pinjam,
                                'stok_sebelum_transaksi' => $barang->stok_saat_ini,
                                'stok_setelah_transaksi' => $barang->stok_saat_ini + $detail->jumlah_pinjam,
                                'remarks' => "Dikembalikan (Baik): " . $record->nomor_peminjaman,
                                'created_by' => Auth::id(),
                            ]);

                            $barang->increment('stok_saat_ini', $detail->jumlah_pinjam);
                        }

                        $record->update([
                            'status' => 'dikembalikan_baik',
                            'tanggal_kembali' => now(),
                        ]);
                        Notification::make()->title('Pengembalian diterima. Stok dipulihkan.')->success()->send();
                    }),

                \Filament\Actions\Action::make('terima_rusak')
                    ->label('Terima (Rusak)')
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-circle')
                    ->visible(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return $record->status === 'menunggu_pengecekan' && $user->hasAnyRole(['super_admin', 'admin_unit']);
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Barang dinyatakan rusak. Stok TIDAK AKAN dipulihkan.')
                    ->action(function ($record) {
                        foreach ($record->details as $detail) {
                            $barang = Barang::find($detail->barang_id);
                            
                            StockTransaction::create([
                                'barang_id' => $barang->id,
                                'type' => 'opname',
                                'quantity' => 0, 
                                'stok_sebelum_transaksi' => $barang->stok_saat_ini,
                                'stok_setelah_transaksi' => $barang->stok_saat_ini,
                                'remarks' => "Insiden Pengembalian Rusak: " . $record->nomor_peminjaman,
                                'created_by' => Auth::id(),
                            ]);
                        }

                        $record->update([
                            'status' => 'dikembalikan_rusak',
                            'tanggal_kembali' => now(),
                        ]);
                        Notification::make()->title('Pengembalian diterima sebagai RUSAK.')->success()->send();
                    }),

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
