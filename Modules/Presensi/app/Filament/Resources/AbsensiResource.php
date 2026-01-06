<?php

namespace Modules\Presensi\Filament\Resources;

use Modules\Presensi\Filament\Resources\AbsensiResource\Pages;
use Modules\Presensi\Models\Absensi;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

// Use Global Actions if Table Actions are not found

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Presensi';
    }

    public static function getModelLabel(): string
    {
        return 'Absensi';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasAnyRole(['kepala_sekolah', 'koor_jenjang'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->toArray();
                return $query->whereHas('user.employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            // If no units assigned, restricted to self
            return $query->where('user_id', $user->id);
        }

        // Default: Staff sees only own data
        return $query->where('user_id', $user->id);
    }

    public static function canCreate(): bool
    {
        $panel = \Filament\Facades\Filament::getCurrentPanel()->getId();

        // Staff panel: allow create
        if ($panel === 'staff') {
            return true;
        }

        // Admin panel: only super_admin can create
        if ($panel === 'admin') {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            return $user->hasRole('super_admin');
        }

        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Absensi')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->default(Auth::id())
                            ->required()
                            ->label('Nama Pegawai')
                            ->disabled(fn() => !Auth::user()->hasAnyRole(['super_admin']))
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now())
                            ->label('Tanggal')
                            ->disabled(fn() => !Auth::user()->hasAnyRole(['super_admin']))
                            ->dehydrated(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'hadir' => 'Hadir',
                                'izin' =>  'Izin',
                                'sakit' => 'Sakit',
                                'alpha' => 'Alpha',
                            ])
                            ->required()
                            ->default('hadir')
                            ->native(false),

                        Forms\Components\TimePicker::make('jam_masuk')
                            ->default(now())
                            ->required(fn($get) => $get('status') === 'hadir'),

                        Forms\Components\TimePicker::make('jam_keluar'),

                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('uraian_harian')
                            ->label('Uraian Harian')
                            ->placeholder('Tuliskan aktivitas harian Anda...')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'orderedList',
                                'italic',
                                'redo',
                                'undo',
                            ])
                            ->visible(fn($get) => $get('status') === 'hadir')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        'alpha' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->time(),

                Tables\Columns\TextColumn::make('jam_keluar')
                    ->time(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(30),

                Tables\Columns\TextColumn::make('uraian_harian')
                    ->label('Uraian Harian')
                    ->html()
                    ->limit(50)
                    ->tooltip(fn($record) => strip_tags($record->uraian_harian ?? ''))
                    ->placeholder('Tidak ada uraian'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ]),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date) => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date) => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->hidden(function () {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin' && !$user->hasRole('super_admin');
                    }),
                \Filament\Actions\DeleteAction::make()
                    ->hidden(function () {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin' && !$user->hasRole('super_admin');
                    }),
            ])
            ->groupedBulkActions([
                \Filament\Actions\DeleteBulkAction::make()
                    ->hidden(function () {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        return \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin' && !$user->hasRole('super_admin');
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
