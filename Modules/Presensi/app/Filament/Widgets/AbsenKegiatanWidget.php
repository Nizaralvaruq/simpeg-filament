<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Modules\Presensi\Models\AbsensiKegiatan;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class AbsenKegiatanWidget extends BaseWidget
{
    protected static ?int $sort = 12;
    protected int | string | array $columnSpan = 'full';
    
    // Jangan tampilkan otomatis di dashboard
    protected static bool $isDiscovered = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = AbsensiKegiatan::with(['user.employee.units', 'kegiatan'])
                    ->latest('jam_absen');
                
                /** @var User $user */
                $user = Auth::user();

                if (!$user) {
                    return $query->whereRaw('1=0');
                }

                // 1. Staff: Hanya melihat data diri sendiri
                if ($user->hasRole('staff')) {
                    return $query->where('user_id', $user->id);
                }

                // 2. Unit Admins / Coordinators / Kepala Sekolah: Lihat data unit masing-masing
                if ($user->hasAnyRole(['koor_jenjang', 'admin_unit', 'kepala_sekolah'])) {
                    if ($user->employee && $user->employee->units->isNotEmpty()) {
                        $unitIds = $user->employee->units->pluck('id')->all();
                        return $query->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                    }
                    return $query->whereRaw('1=0');
                }

                // 3. Super Admin / Ketua PSDM: Lihat semua data
                return $query;
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => !($user = Auth::user()) || !($user instanceof User && $user->hasRole('staff'))),
                TextColumn::make('kegiatan.nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jam_absen')
                    ->label('Waktu Absen')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('kegiatan.lokasi')
                    ->label('Lokasi')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir' => 'danger',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => str($state)->replace('_', ' ')->title()),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                    ]),
                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->schema([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('jam_absen', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('jam_absen', '<=', $date),
                            );
                    })
            ]);
    }
}
