<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalCategory;
use Modules\PenilaianKinerja\Models\AppraisalIndicator;
use Modules\PenilaianKinerja\Models\AppraisalResult;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Forms\Components\Radio;
use Modules\PenilaianKinerja\Filament\Resources\MyAppraisalAssignmentResource\Pages;
use Filament\Forms\Components\Repeater; // Added for the new form method

class MyAppraisalAssignmentResource extends Resource
{
    protected static ?string $model = AppraisalAssignment::class;

    protected static ?int $navigationSort = 70;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-pencil-square';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penilaian Kinerja';
    }

    public static function getModelLabel(): string
    {
        return 'Tugas Penilaian Saya';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tugas Penilaian Saya';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Penilaian')
                ->schema([
                    Text::make('session_name')
                        ->label('Sesi')
                        ->state(fn($record) => $record?->session?->name),
                    Text::make('ratee_name')
                        ->label('Pegawai yang Dinilai')
                        ->state(fn($record) => $record?->ratee?->nama),
                    Text::make('relation_type_label')
                        ->label('Hubungan')
                        ->state(fn($record) => match ($record?->relation_type) {
                            'self' => 'Diri Sendiri',
                            'peer' => 'Rekan',
                            'superior' => 'Atasan',
                            default => '-',
                        }),
                ])->columns(2),

            Section::make('Hasil Penilaian')
                ->visible(fn($record) => $record?->status === 'completed')
                ->schema([
                    Repeater::make('results')
                        ->relationship('results')
                        ->schema([
                            Text::make('indicator_text')
                                ->label('Indikator')
                                ->state(fn($record) => $record?->indicator?->indicator_text),
                            Text::make('indicator_score')
                                ->label('Skor')
                                ->state(fn($record) => $record?->score),
                        ])
                        ->columns(2)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                ])
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('rater_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session.name')
                    ->label('Sesi')
                    ->sortable(),
                TextColumn::make('ratee.nama')
                    ->label('Pegawai yang Dinilai')
                    ->weight('bold'),
                TextColumn::make('relation_type')
                    ->label('Sebagai')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'self' => 'info',
                        'peer' => 'warning',
                        'superior' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'self' => 'Diri Sendiri',
                        'peer' => 'Rekan',
                        'superior' => 'Atasan',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'danger',
                        'completed' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Belum Diisi',
                        'completed' => 'Selesai',
                    }),
            ])
            ->recordActions([
                Action::make('fill_rating')
                    ->label('Beri Nilai')
                    ->icon('heroicon-m-star')
                    ->color('primary')
                    ->visible(fn(AppraisalAssignment $record) => $record->status === 'pending')
                    ->schema(function (AppraisalAssignment $record) {
                        // Dynamically build the form based on categories and indicators
                        $categories = AppraisalCategory::with('indicators')->orderBy('weight', 'desc')->get();
                        $fields = [];

                        foreach ($categories as $category) {
                            $indicatorFields = [];
                            foreach ($category->indicators as $indicator) {
                                $indicatorFields[] = Radio::make('indicator_' . $indicator->id)
                                    ->label($indicator->indicator_text)
                                    ->options([
                                        1 => '1 (Sangat Kurang)',
                                        2 => '2 (Kurang)',
                                        3 => '3 (Baik)',
                                        4 => '4 (Sangat Baik)',
                                        5 => '5 (Istimewa)',
                                    ])
                                    ->required()
                                    ->inline();
                            }

                            if (!empty($indicatorFields)) {
                                $fields[] = Section::make($category->name)
                                    ->schema($indicatorFields);
                            }
                        }

                        return $fields;
                    })
                    ->action(function (array $data, AppraisalAssignment $record) {
                        foreach ($data as $key => $value) {
                            if (str_starts_with($key, 'indicator_')) {
                                $indicatorId = str_replace('indicator_', '', $key);
                                AppraisalResult::updateOrCreate(
                                    [
                                        'assignment_id' => $record->id,
                                        'indicator_id' => $indicatorId,
                                    ],
                                    ['score' => $value]
                                );
                            }
                        }

                        $record->update(['status' => 'completed']);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAppraisalAssignments::route('/'),
        ];
    }
}
