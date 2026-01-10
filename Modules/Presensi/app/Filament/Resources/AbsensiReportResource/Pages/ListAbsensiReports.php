<?php

namespace Modules\Presensi\Filament\Resources\AbsensiReportResource\Pages;

use Modules\Presensi\Filament\Resources\AbsensiReportResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Presensi\Exports\AbsensiExport;
use Modules\Presensi\Models\Absensi;

class ListAbsensiReports extends ListRecords
{
    protected static string $resource = AbsensiReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Laporan')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Logic to export based on current filters?
                    // Accessing filters from Action is tricky without form state.
                    // For now, simple export of ALL or Current Month.
                    // Ideally we pass filter state.

                    // We'll export the *Summary* or *Detailed Logs*?
                    // User likely wants Detailed Reprot for the period.

                    // Let's get the filter values from the Table? 
                    // Difficult in Header Action.
                    // Alternative: Use a Modal Form in Export Action.
                })
                ->schema([
                    \Filament\Forms\Components\DatePicker::make('start_date')->default(now()->startOfMonth()),
                    \Filament\Forms\Components\DatePicker::make('end_date')->default(now()->endOfMonth()),
                ])
                ->action(function (array $data) {
                    $query = Absensi::query()
                        ->whereBetween('tanggal', [$data['start_date'], $data['end_date']]);
                        
                     // Apply Scoping
                    /** @var \App\Models\User $user */
                    $user = \Illuminate\Support\Facades\Auth::user();
                    if ($user && $user->hasAnyRole(['koor_jenjang', 'admin_unit', 'kepala_sekolah'])) {
                        if ($user->employee && $user->employee->units->isNotEmpty()) {
                            $unitIds = $user->employee->units->pluck('id');
                            $query->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                        }
                    }

                    return Excel::download(new AbsensiExport($query->get()), 'Laporan_Absensi.xlsx');
                })
        ];
    }
}
