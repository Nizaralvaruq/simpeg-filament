<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource\Pages;
use Modules\PenilaianKinerja\Models\PerformanceScore;
use Modules\Kepegawaian\Models\DataInduk;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PerformanceScoreResource extends Resource
{
    protected static ?string $model = PerformanceScore::class;
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-presentation-chart-line';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penilaian 360';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penilaian Kinerja';
    }

    public static function getNavigationLabel(): string
    {
        return 'Penilaian Kinerja';
    }

    public static function getModelLabel(): string
    {
        return 'Penilaian';
    }

    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $isStaff = $user->hasRole('staff');

        $ratingOptions = [
            '1' => '1 (Sangat Kurang)',
            '2' => '2 (Kurang)',
            '3' => '3 (Cukup)',
            '4' => '4 (Baik)',
            '5' => '5 (Sangat Baik)',
        ];

        return $schema->components([
            Section::make('Informasi Penilaian')
                ->schema([
                    Select::make('data_induk_id')
                        ->label('Pegawai yang Dinilai')
                        ->default(request()->query('data_induk_id')) // Ambil dari URL jika ada
                        ->options(function () use ($user, $isStaff) {
                            $query = DataInduk::query()->where('id', '!=', $user->employee?->id);

                            if ($isStaff && $user->employee) {
                                $unitIds = $user->employee->units->pluck('id');
                                $query->whereHas('units', function ($q) use ($unitIds) {
                                    $q->whereIn('units.id', $unitIds);
                                });
                            }

                            return $query->pluck('nama', 'id');
                        })
                        ->required()
                        ->searchable(),

                    DatePicker::make('periode')
                        ->label('Periode Penilaian')
                        ->native(false)
                        ->displayFormat('F Y')
                        ->format('Y-m')
                        ->default(now()->format('Y-m'))
                        ->required(),

                    Hidden::make('penilai_id')
                        ->default($user->id),

                    Hidden::make('tipe_penilai')
                        ->default($isStaff ? 'rekan' : 'atasan'),
                ])->columns(2),

            Section::make('Kualitas & Produktivitas')
                ->description('Evaluasi terhadap hasil kerja dan efisiensi waktu.')
                ->schema([
                    Radio::make('kualitas_hasil')
                        ->label('1. Kualitas Hasil Pekerjaan (Ketepatan & Kerapihan)')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                    Radio::make('ketelitian')
                        ->label('2. Tingkat Ketelitian dalam bekerja')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                    Radio::make('kuantitas_hasil')
                        ->label('3. Kuantitas Hasil Kerja (Memenuhi Target)')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                    Radio::make('ketepatan_waktu')
                        ->label('4. Ketepatan Waktu dalam menyelesaikan tugas')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                ]),

            Section::make('Kedisplinan & Etika')
                ->description('Evaluasi terhadap kehadiran dan perilaku profesional.')
                ->schema([
                    Radio::make('kehadiran')
                        ->label('5. Tingkat Kehadiran & Absensi')
                        ->options($ratingOptions)
                        ->inline()
                        ->required()
                        ->visible(!$isStaff),
                    Radio::make('kepatuhan_aturan')
                        ->label('6. Kepatuhan terhadap SOP & Aturan Instansi')
                        ->options($ratingOptions)
                        ->inline()
                        ->required()
                        ->visible(!$isStaff),
                    Radio::make('etika_kerja')
                        ->label('7. Etika dalam bekerja dan sopan santun')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                    Radio::make('tanggung_jawab')
                        ->label('8. Tanggung Jawab terhadap tugas yang diberikan')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                ]),

            Section::make('Keahlian & Kerjasama')
                ->description('Evaluasi terhadap kemampuan komunikasi dan tim.')
                ->schema([
                    Radio::make('komunikasi')
                        ->label('9. Efektifitas Komunikasi (Internal & Eksternal)')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                    Radio::make('kerjasama_tim')
                        ->label('10. Kemampuan bekerjasama dalam tim')
                        ->options($ratingOptions)
                        ->inline()
                        ->required(),
                ]),

            Section::make('Catatan & Feedback')
                ->schema([
                    Textarea::make('catatan')
                        ->label('Keterangan Tambahan / Feedback untuk Pegawai')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->date('M Y'),
                Tables\Columns\TextColumn::make('tipe_penilai')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn($state) => $state === 'atasan' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('average_score')
                    ->label('Skor (IPK)')
                    ->numeric(2)
                    ->sortable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->color(fn($record) => $record->grade_color)
                    ->description(fn($record) => $record->grade_label)
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('penilai.name')
                    ->label('Penilai'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_penilai')
                    ->options([
                        'atasan' => 'Atasan',
                        'rekan' => 'Rekan',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerformanceScores::route('/'),
            'create' => Pages\CreatePerformanceScore::route('/create'),
            'view' => Pages\ViewPerformanceScore::route('/{record}'),
            'edit' => Pages\EditPerformanceScore::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $query = parent::getEloquentQuery();

        if ($user->hasRole('staff')) {
            return $query->where('penilai_id', $user->id)
                ->orWhere('data_induk_id', $user->employee?->id);
        }

        return $query;
    }
}
