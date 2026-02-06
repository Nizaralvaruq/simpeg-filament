<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Leave\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\ActionSize;
use Modules\Leave\Filament\Resources\LeaveRequestResource;

class PendingLeaveRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = 'Permohonan Izin Menunggu';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['kepala_sekolah', 'admin_unit', 'super_admin', 'ketua_psdm']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LeaveRequest::query()
                    ->where('status', 'pending')
                    ->whereHas(
                        'employee.units',
                        function ($query) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if ($user->hasRole('super_admin')) {
                                return $query;
                            }
                            // Filter by user's unit
                            $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                            return $query->whereIn('units.id', $unitIds);
                        }
                    )
                    ->with(['employee'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Nama')
                    ->weight('bold')
                    ->description(fn($record) => $record->leave_type ? ucfirst($record->leave_type) : 'Cuti'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal')
                    ->date('d M')
                    ->description(fn($record) => $record->end_date ? 's/d ' . $record->end_date->format('d M') : ''),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->reason),
            ])
            ->actions([
                \Filament\Actions\Action::make('review')
                    ->label('Tinjau')
                    ->url(fn(LeaveRequest $record): string => LeaveRequestResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
