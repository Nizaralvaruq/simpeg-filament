<?php

namespace Modules\Presensi\Filament\Resources\JadwalPiketResource\Pages;

use Modules\Presensi\Filament\Resources\JadwalPiketResource;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\Presensi\Models\JadwalPiket;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class AssignPiket extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = JadwalPiketResource::class;

    protected string $view = 'presensi::filament.resources.jadwal-piket-resource.pages.assign-piket';

    protected static ?string $title = 'Assign Petugas Piket';

    protected static ?string $breadcrumb = 'Assign Petugas';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = DataInduk::query()->where('status', 'Aktif');
                /** @var \App\Models\User $user */
                $user = Auth::user();

                if ($user->hasRole('super_admin')) {
                    return $query;
                }

                if ($user->hasRole('admin_unit')) {
                    if ($user->employee && $user->employee->units->isNotEmpty()) {
                        $unitIds = $user->employee->units->pluck('id');
                        return $query->whereHas('units', function ($q) use ($unitIds) {
                            $q->whereIn('units.id', $unitIds);
                        });
                    }
                    return $query->whereRaw('1=0');
                }

                // Default: Access Denied
                return $query->whereRaw('1=0');
            })
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('units.name')
                    ->label('Unit')
                    ->badge()
                    ->separator(', '),

                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable(),

                TextColumn::make('nip')
                    ->label('NIP/NPA')
                    ->searchable(),
            ])
            ->filters([
                // Add Unit Filter if needed
            ])
            ->recordActions([
                Action::make('assign')
                    ->label('Jadikan Petugas')
                    ->icon('heroicon-o-plus-circle')
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal Piket')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Select::make('shift')
                            ->label('Shift')
                            ->options([
                                'pagi' => 'Pagi',
                                'siang' => 'Siang',
                                'sore' => 'Sore',
                            ])
                            ->placeholder('Pilih shift (opsional)'),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->placeholder('Catatan (opsional)'),
                    ])
                    ->action(function (DataInduk $record, array $data) {
                        // Check if user account exists
                        if (!$record->user_id) {
                            Notification::make()
                                ->title('Gagal')
                                ->body("Pegawai {$record->nama} belum memiliki akun User. Buat akun terlebih dahulu di menu Kepegawaian.")
                                ->danger()
                                ->send();
                            return;
                        }

                        // Create Schedule
                        $schedule = JadwalPiket::create([
                            'user_id' => $record->user_id,
                            'tanggal' => $data['tanggal'],
                            'shift' => $data['shift'],
                            'keterangan' => $data['keterangan'],
                            'created_by' => Auth::id(),
                        ]);

                        // Notify User (Logic from CreateJadwalPiket hook duplicated/reused here)
                        $record->user->notify(
                            \Filament\Notifications\Notification::make()
                                ->title('Jadwal Piket Baru')
                                ->body("Admin telah menjadwalkan Anda piket pada tanggal: " . \Carbon\Carbon::parse($data['tanggal'])->format('d M Y'))
                                ->info()
                                ->toDatabase()
                        );

                        Notification::make()
                            ->title('Berhasil')
                            ->body("{$record->nama} berhasil dijadwalkan piket.")
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([]);
    }
}
