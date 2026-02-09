<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\SesiPenilaianResource\Pages;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Closure;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SesiPenilaianResource extends Resource
{
    protected static ?string $model = AppraisalSession::class;

    protected static ?int $navigationSort = 40;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penilaian Kinerja';
    }

    public static function getModelLabel(): string
    {
        return 'Sesi Penilaian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sesi Penilaian';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Wizard::make([
                \Filament\Schemas\Components\Wizard\Step::make('Identitas Sesi')
                    ->description('Nama dan jadwal sesi penilaian')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Sesi')
                            ->required()
                            ->placeholder('Contoh: Penilaian Q1 2025')
                            ->maxLength(255),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->label('Tanggal Selesai')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'Draft' => 'Draft',
                                        'Published' => 'Published',
                                        'Closed' => 'Closed',
                                    ])
                                    ->default('Draft')
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label('Aktifkan Sesi Ini')
                                    ->default(false),
                            ]),
                    ]),

                \Filament\Schemas\Components\Wizard\Step::make('Pengaturan Bobot')
                    ->description('Bobot agregasi skor (Total harus 100%)')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('superior_weight')
                                    ->label('Atasan (%)')
                                    ->numeric()
                                    ->default(50)
                                    ->required()
                                    ->live()
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $total = (int) $get('superior_weight') + (int) $get('peer_weight') + (int) $get('self_weight');
                                            if ($total !== 100) {
                                                $fail("Total bobot harus 100% (Saat ini: {$total}%)");
                                            }
                                        },
                                    ]),
                                TextInput::make('peer_weight')
                                    ->label('Rekan Sejawat (%)')
                                    ->numeric()
                                    ->default(30)
                                    ->required()
                                    ->live()
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $total = (int) $get('superior_weight') + (int) $get('peer_weight') + (int) $get('self_weight');
                                            if ($total !== 100) {
                                                $fail("Total bobot harus 100% (Saat ini: {$total}%)");
                                            }
                                        },
                                    ]),
                                TextInput::make('self_weight')
                                    ->label('Diri Sendiri (%)')
                                    ->numeric()
                                    ->default(20)
                                    ->required()
                                    ->live()
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $total = (int) $get('superior_weight') + (int) $get('peer_weight') + (int) $get('self_weight');
                                            if ($total !== 100) {
                                                $fail("Total bobot harus 100% (Saat ini: {$total}%)");
                                            }
                                        },
                                    ]),
                            ])
                    ])
            ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Sesi')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Published' => 'success',
                        'Closed' => 'danger',
                    }),
            ])
            ->striped()
            ->persistFiltersInSession()
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Ubah'),
                    DeleteAction::make()
                        ->label('Hapus'),
                ])->label('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSesiPenilaian::route('/'),
            'create' => Pages\CreateSesiPenilaian::route('/create'),
            'edit' => Pages\EditSesiPenilaian::route('/{record}/edit'),
        ];
    }
}
