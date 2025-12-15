<?php

namespace Modules\Kepegawaian\Filament\Resources;

use Modules\Kepegawaian\Filament\Resources\LeaveRequestResource\Pages;
use Modules\Kepegawaian\Models\LeaveRequest;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan Cuti';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan Cuti';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Super Admin & Admin HR: View ALL
        if ($user->hasRole('super_admin') || $user->hasRole('admin_hr')) {
            return $query;
        }

        // 2. Kepala Sekolah & Koor Jenjang: View Unit Requests
        if ($user->hasRole('kepala_sekolah') || $user->hasRole('koor_jenjang')) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                return $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            // If they have no unit assigned, they shouldn't see anything?
            // Or default to empty.
            return $query->whereRaw('1=0');
        }

        // 3. Staff: View Own Requests
        if ($user->hasRole('staff')) {
            if ($user->employee) {
                return $query->where('data_induk_id', $user->employee->id);
            }
            return $query->whereRaw('1=0');
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Cuti')
                    ->schema([
                        Forms\Components\Hidden::make('data_induk_id')
                            ->default(function () {
                                /** @var \App\Models\User $user */
                                $user = auth()->user();
                                return $user->employee?->id;
                            }),

                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Mulai Tanggal')
                                    ->required()
                                    ->minDate(now()),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Sampai Tanggal')
                                    ->required()
                                    ->afterOrEqual('start_date'),
                            ]),

                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Cuti')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Persetujuan')
                    ->visible(function () {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();
                        return $user->hasAnyRole(['admin_hr', 'kepala_sekolah']);
                    })
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Menunggu',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Catatan / Alasan Penolakan')
                            ->visible(fn($get) => $get('status') === 'rejected'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Nama Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label('Mulai'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label('Selesai'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
