<?php

namespace Modules\Presensi\Filament\Resources\KegiatanResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class AbsensiKegiatansRelationManager extends RelationManager
{
    protected static string $relationship = 'absensiKegiatans';

    protected static ?string $title = 'Laporan Kehadiran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('Pegawai'),
                \Filament\Forms\Components\DateTimePicker::make('jam_absen')
                    ->label('Jam Absen'),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                    ])
                    ->required(),
                \Filament\Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                /** @var \App\Models\User $user */
                $user = \Illuminate\Support\Facades\Auth::user();

                // Admin Unit & Koor Jenjang: Only see attendance records from employees in their units
                if ($user && $user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                    if ($user->employee && $user->employee->units->isNotEmpty()) {
                        $unitIds = $user->employee->units->pluck('id');
                        $query->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                    } else {
                        $query->whereRaw('1=0'); // No units assigned, show nothing
                    }
                }

                return $query;
            })
            ->recordTitleAttribute('status')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_absen')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                    ]),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Tambah Kehadiran Manual')
                    ->modalHeading('Input Kehadiran Manual')
                    ->icon('heroicon-o-plus-circle')
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']);
                    }),
                \Filament\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return $user && $user->hasAnyRole(['super_admin', 'ketua_psdm']);
                    })
                    ->action(function ($livewire) {
                        $kegiatan = $livewire->ownerRecord;
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \Modules\Presensi\Exports\AbsensiKegiatanExport($kegiatan->id),
                            'absensi-' . str($kegiatan->nama_kegiatan)->slug() . '.xlsx'
                        );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
