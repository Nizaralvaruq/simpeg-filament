<?php

namespace Modules\Kepegawaian\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
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
        if ($user->hasRole('super_admin')) {
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
                        Forms\Components\Select::make('data_induk_id')
                            ->label('Nama Pegawai')
                            ->relationship('employee', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('super_admin'))
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('data_induk_id')
                            ->default(fn () => auth()->user()->employee?->id)
                            ->visible(fn () => ! auth()->user()->hasRole('super_admin')),

                        Forms\Components\DatePicker::make('tanggal_resign')
                            ->required()
                            ->label('Tanggal Resign')
                            ->minDate(now()),

                        Forms\Components\Textarea::make('alasan')
                            ->required()
                            ->label('Alasan Resign')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('status')
                            ->default('diajukan')
                            ->dehydrated(true)
                            ->visible(fn () => ! auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                    ]),

                \Filament\Schemas\Components\Section::make('Persetujuan')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'diajukan' => 'Diajukan',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Textarea::make('keterangan_tindak_lanjut')
                            ->label('Catatan / Alasan Penolakan')
                            ->visible(fn ($get) => $get('status') === 'ditolak')
                            ->required(fn ($get) => $get('status') === 'ditolak'),
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
                    ->label('Unit Kerja')
                    ->badge(),
                Tables\Columns\TextColumn::make('employee.jabatan')
                    ->label('Jabatan')
                    ->badge(),
                Tables\Columns\TextColumn::make('tanggal_resign')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
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
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) =>
                            auth()->user()->hasAnyRole(['super_admin', 'admin'])
                            && $record->status === 'diajukan'
                        )
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'disetujui',
                            ]);
                            // UPDATE DATA INDUK
                            $record->employee->update([
                                'status' => 'resign',
                                'keterangan' => $record->alasan,
                            ]);
                        }),

                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('keterangan_tindak_lanjut')
                                ->label('Alasan Penolakan')
                                ->required(),
                        ])
                        ->visible(fn ($record) =>
                            auth()->user()->hasAnyRole(['super_admin', 'admin'])
                            && $record->status === 'diajukan'
                        )
                        ->action(fn ($record, array $data) =>
                            $record->update([
                                'status' => 'ditolaK',
                                'keterangan_tindak_lanjut' => $data['keterangan_tindak_lanjut'],
                            ])
                        ),
                    ]),
            ])
            ->actionsColumnLabel('Aksi')
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
