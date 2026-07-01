<?php

namespace Modules\MasterData\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\MasterData\Models\UnitType;
use Filament\Forms;

class TipeJenjangWidget extends BaseWidget
{
    protected static ?string $heading = 'Kelola Tipe Jenjang';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    // Jangan tampilkan di dashboard, hanya di halaman Jenjang
    protected static bool $isDiscovered = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(UnitType::query()->withCount('units'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tipe Jenjang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('units_count')
                    ->label('Jumlah Jenjang')
                    ->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tipe Jenjang')
                            ->required()
                            ->unique(UnitType::class, 'name', ignoreRecord: true)
                            ->maxLength(255),
                    ]),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\CreateAction::make()
                    ->model(UnitType::class)
                    ->label('Tambah Tipe Jenjang')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tipe Jenjang')
                            ->required()
                            ->unique(UnitType::class, 'name')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
