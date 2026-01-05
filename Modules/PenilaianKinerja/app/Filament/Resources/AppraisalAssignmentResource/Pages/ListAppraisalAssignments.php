<?php

namespace Modules\PenilaianKinerja\Filament\Resources\AppraisalAssignmentResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\MasterData\Models\Unit;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ListAppraisalAssignments extends ListRecords
{
    protected static string $resource = AppraisalAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('bulk_generate')
                ->label('Generate Otomatis')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->form([
                    Select::make('session_id')
                        ->label('Sesi Penilaian')
                        ->options(AppraisalSession::where('is_active', true)->whereNotNull('name')->pluck('name', 'id'))
                        ->required(),
                    Select::make('unit_id')
                        ->label('Unit / Jenjang')
                        ->options(function () {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if ($user->hasRole('koor_jenjang')) {
                                return $user->employee->units()->whereNotNull('name')->pluck('name', 'id');
                            }
                            return Unit::whereNotNull('name')->pluck('name', 'id');
                        })
                        ->required()
                        ->reactive(),
                    CheckboxList::make('types')
                        ->label('Jenis Penilaian yang di-Generate')
                        ->options([
                            'self' => 'Self Assessment (Penilaian Diri)',
                            'peer' => 'Peer Review (Rekan Sejawat)',
                            'superior' => 'Superior (Penilaian Atasan)',
                        ])
                        ->required()
                        ->reactive(),
                    Select::make('superior_id')
                        ->label('Pilih Atasan/Penilai (Untuk Superior Review)')
                        ->options(function (Get $get) {
                            $unitId = $get('unit_id');
                            if (!$unitId) return [];

                            return User::whereHas('employee.units', fn($q) => $q->where('units.id', $unitId))
                                ->whereHas('roles', fn($q) => $q->whereIn('name', ['koor_jenjang', 'admin', 'super_admin']))
                                ->whereNotNull('name')
                                ->pluck('name', 'id');
                        })
                        ->visible(fn(Get $get) => in_array('superior', $get('types') ?? []))
                        ->required(fn(Get $get) => in_array('superior', $get('types') ?? [])),
                ])
                ->action(function (array $data) {
                    $employees = DataInduk::whereHas('units', fn($q) => $q->where('units.id', $data['unit_id']))
                        ->where('status', 'Aktif')
                        ->get();

                    if ($employees->isEmpty()) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('Tidak ada pegawai aktif di unit ini.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $count = 0;
                    foreach ($employees as $employee) {
                        // 1. Generate Self
                        if (in_array('self', $data['types'])) {
                            if ($employee->user_id) {
                                AppraisalAssignment::updateOrCreate([
                                    'session_id' => $data['session_id'],
                                    'rater_id' => $employee->user_id,
                                    'ratee_id' => $employee->id,
                                    'relation_type' => 'self',
                                ], ['status' => 'pending']);
                                $count++;
                            }
                        }

                        // 2. Generate Superior
                        if (in_array('superior', $data['types']) && !empty($data['superior_id'])) {
                            // Ensure superior is not the ratee themselves for superior type
                            // Actually it's allowed but usually different.
                            AppraisalAssignment::updateOrCreate([
                                'session_id' => $data['session_id'],
                                'rater_id' => $data['superior_id'],
                                'ratee_id' => $employee->id,
                                'relation_type' => 'superior',
                            ], ['status' => 'pending']);
                            $count++;
                        }

                        // 3. Generate Peers
                        if (in_array('peer', $data['types'])) {
                            foreach ($employees as $peer) {
                                if ($peer->id !== $employee->id && $peer->user_id) {
                                    AppraisalAssignment::updateOrCreate([
                                        'session_id' => $data['session_id'],
                                        'rater_id' => $peer->user_id,
                                        'ratee_id' => $employee->id,
                                        'relation_type' => 'peer',
                                    ], ['status' => 'pending']);
                                    $count++;
                                }
                            }
                        }
                    }

                    Notification::make()
                        ->title('Berhasil')
                        ->body("Berhasil meng-generate $count tugas penilaian.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
