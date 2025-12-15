<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResignResource\Pages;
use Modules\Pegawai\Models\Resign;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class ResignResource extends Resource
{
    protected static ?string $model = Resign::class;

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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('admin_hr')) {
            return $query;
        }

        if ($user->hasRole('kepala_sekolah') || $user->hasRole('koor_jenjang')) {
            if ($user->employee) {
                $unitIds = $user->employee->units->pluck('id');
                return $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            return $query->whereRaw('1=0');
        }

        // Staff only see their own
        if ($user->hasRole('staff') && $user->employee) {
            return $query->where('data_induk_id', $user->employee->id);
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
                            ->default(fn() => auth()->user()->employee?->id),

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
                    ->visible(fn() => auth()->user()->hasAnyRole(['admin_hr', 'kepala_sekolah']))
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
                    ->label('Unit')
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
