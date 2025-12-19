<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\PerformanceAssessmentResource\Pages;
use Modules\PenilaianKinerja\Models\PerformanceAssessment;
use Modules\PenilaianKinerja\Models\KpiPeriod;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PerformanceAssessmentResource extends Resource
{
    protected static ?string $model = PerformanceAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Penilaian Kinerja';

    protected static ?string $modelLabel = 'Penilaian Kinerja';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Informasi Umum')
                    ->schema([
                        Components\Select::make('period_id')
                            ->label('Periode')
                            ->options(KpiPeriod::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Components\Select::make('data_induk_id')
                            ->label('Pegawai')
                            ->relationship('employee', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Components\Hidden::make('assessor_id')
                            ->default(Auth::id()),
                        Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Diajukan',
                                'finalized' => 'Selesai',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(3),

                Components\Section::make('Kriteria Penilaian')
                    ->description('Masukkan skor untuk setiap indikator yang tersedia.')
                    ->schema([
                        Components\Repeater::make('details')
                            ->relationship()
                            ->schema([
                                Components\Select::make('kpi_indicator_id')
                                    ->label('Indikator')
                                    ->relationship('indicator', 'name')
                                    ->required()
                                    ->searchable(),
                                Components\TextInput::make('score')
                                    ->label('Skor')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                Components\Textarea::make('note')
                                    ->label('Catatan')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => $state['kpi_indicator_id'] ?? null),
                    ]),

                Components\Section::make('Hasil & Kesimpulan')
                    ->schema([
                        Components\TextInput::make('total_score')
                            ->label('Total Nilai')
                            ->numeric()
                            ->readOnly(),
                        Components\Textarea::make('comment')
                            ->label('Komentar/Saran')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period.name')
                    ->label('Periode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Nilai')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'finalized' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Periode')
                    ->relationship('period', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerformanceAssessments::route('/'),
            'create' => Pages\CreatePerformanceAssessment::route('/create'),
            'edit' => Pages\EditPerformanceAssessment::route('/{record}/edit'),
            'view' => Pages\ViewPerformanceAssessment::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return parent::getEloquentQuery();
        }

        if ($user->hasRole('staff')) {
            return parent::getEloquentQuery()->where('data_induk_id', $user->employee?->id);
        }

        return parent::getEloquentQuery()->where(function ($query) use ($user) {
            $query->where('assessor_id', $user->id);
        });
    }
}
