<?php

namespace Modules\Kepegawaian\Filament\Resources;

use Modules\Kepegawaian\Filament\Resources\ResignResource\Pages;
use Modules\Kepegawaian\Models\Resign;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ResignResource extends Resource
{
    protected static ?string $model = Resign::class;
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-arrow-left-on-rectangle';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan Resign';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan Resign';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Super Admin & Admin HR: View ALL
<<<<<<< HEAD
        if ($user->hasRole('super_admin')) {
=======
        if ($user->hasRole('super_admin') || $user->hasRole('super_admin')) {
>>>>>>> origin/branch_dhevi
            return $query;
        }

        // 2. Kepala Sekolah & Koor Jenjang: View Unit Resigns
        if ($user->hasRole('kepala_sekolah') || $user->hasRole('koor_jenjang')) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                return $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            return $query->whereRaw('1=0');
        }

        // 3. Staff: View Own Resigns
        if ($user->hasRole('staff')) {
            if ($user->employee) {
                return $query->where('data_induk_id', $user->employee->id);
            }
            return $query->whereRaw('1=0');
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Pengajuan')
                    ->schema([
                        // Hidden field for employee ID, filled automatically on create
                        Forms\Components\Hidden::make('data_induk_id')
                            ->default(function () {
                                /** @var \App\Models\User $user */
                                $user = Auth::user();
                                return $user->employee?->id;
                            }),

                        Forms\Components\DatePicker::make('tanggal_resign')
                            ->required()
                            ->label('Tanggal Resign')
                            ->minDate(now()),

                        Forms\Components\Textarea::make('alasan')
                            ->required()
                            ->label('Alasan Resign')
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Persetujuan')
                    ->visible(function () {
                        /** @var \App\Models\User $user */
<<<<<<< HEAD
                        $user = Auth::user();
                        return $user->hasRole('super_admin');
=======
                        $user = auth()->user();
                        return $user->hasAnyRole(['super_admin', 'kepala_sekolah']);
>>>>>>> origin/branch_dhevi
                    })
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'diajukan' => 'Diajukan',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('keterangan_tindak_lanjut')
                            ->label('Catatan Approval'),
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
                Tables\Columns\TextColumn::make('employee.units.name')
                    ->label('Jenjang')
                    ->badge(),
                Tables\Columns\TextColumn::make('tanggal_resign')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
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
            'index' => Pages\ListResigns::route('/'),
            'create' => Pages\CreateResign::route('/create'),
            'edit' => Pages\EditResign::route('/{record}/edit'),
        ];
    }
}
