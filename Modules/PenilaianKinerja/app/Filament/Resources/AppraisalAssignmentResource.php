<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalAssignmentResource\Pages;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Modules\Kepegawaian\Models\DataInduk;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AppraisalAssignmentResource extends Resource
{
    protected static ?string $model = AppraisalAssignment::class;

    protected static ?int $navigationSort = 60;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penilaian 360';
    }

    public static function getModelLabel(): string
    {
        return 'Penugasan Penilai';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penugasan Penilai';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    Select::make('session_id')
                        ->label('Sesi Penilaian')
                        ->relationship('session', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Grid::make(2)
                        ->schema([
                            Select::make('rater_id')
                                ->label('Penilai (Akun User)')
                                ->options(function () {
                                    /** @var \App\Models\User $user */
                                    $user = Auth::user();
                                    $query = User::query();

                                    if ($user->hasRole('koor_jenjang')) {
                                        $unitIds = $user->employee->units->pluck('id');
                                        $query->whereHas('employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                                    }

                                    return $query->whereNotNull('name')->pluck('name', 'id');
                                })
                                ->required()
                                ->searchable()
                                ->preload(),
                            Select::make('ratee_id')
                                ->label('Yang Dinilai (Pegawai)')
                                ->options(function () {
                                    /** @var \App\Models\User $user */
                                    $user = Auth::user();
                                    $query = DataInduk::query();

                                    if ($user->hasRole('koor_jenjang')) {
                                        $unitIds = $user->employee->units->pluck('id');
                                        $query->whereHas('units', fn($q) => $q->whereIn('units.id', $unitIds));
                                    }

                                    return $query->whereNotNull('nama')->pluck('nama', 'id');
                                })
                                ->required()
                                ->searchable()
                                ->preload(),
                        ]),
                    Grid::make(2)
                        ->schema([
                            Select::make('relation_type')
                                ->label('Hubungan')
                                ->options([
                                    'self' => 'Self Assessment',
                                    'peer' => 'Peer Review (Rekan)',
                                    'superior' => 'Superior (Atasan)',
                                ])
                                ->required(),
                            Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'completed' => 'Completed',
                                ])
                                ->default('pending')
                                ->required(),
                        ]),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session.name')
                    ->label('Sesi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('rater.name')
                    ->label('Penilai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ratee.nama')
                    ->label('Yang Dinilai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('relation_type')
                    ->label('Hubungan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'self' => 'info',
                        'peer' => 'warning',
                        'superior' => 'success',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('session_id')
                    ->label('Filter Sesi')
                    ->relationship('session', 'name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->hasRole('koor_jenjang')) {
            $unitIds = $user->employee->units->pluck('id');
            $query->whereHas('ratee.units', fn($q) => $q->whereIn('units.id', $unitIds));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppraisalAssignments::route('/'),
            'create' => Pages\CreateAppraisalAssignment::route('/create'),
            'edit' => Pages\EditAppraisalAssignment::route('/{record}/edit'),
        ];
    }
}
