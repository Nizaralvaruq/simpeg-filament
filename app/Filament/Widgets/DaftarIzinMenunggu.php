<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Leave\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Leave\Filament\Resources\LeaveRequestResource;

class DaftarIzinMenunggu extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = 'Permohonan Izin Menunggu';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:DaftarIzinMenunggu');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);

                return LeaveRequest::query()
                    ->where('status', 'pending')
                    ->when(!$isGlobalAdmin, function ($q) use ($user) {
                        $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                        if (!empty($unitIds)) {
                            $q->whereHas('employee.units', fn($sq) => $sq->whereIn('units.id', $unitIds));
                        } else {
                            $q->whereRaw('1=0');
                        }
                    })
                    ->with(['employee']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Nama')
                    ->weight('bold')
                    ->description(fn($record) => $record->leave_type ? ucfirst($record->leave_type) : 'Cuti')
                    ->url(fn(LeaveRequest $record): string => LeaveRequestResource::getUrl('edit', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal')
                    ->date('d M')
                    ->description(fn($record) => $record->end_date ? 's/d ' . $record->end_date->format('d M') : ''),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->reason),
            ])
            ->paginated(false);
    }
}
