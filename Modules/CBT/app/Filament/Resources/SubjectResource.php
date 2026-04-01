<?php

namespace Modules\CBT\Filament\Resources;

use Modules\CBT\Models\Subject;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Modules\CBT\Filament\Resources\SubjectResource\Pages;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-book-open';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CBT / Ujian';
    }

    public static function getModelLabel(): string
    {
        return 'Mata Pelajaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mata Pelajaran (CBT)';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Informasi Mata Pelajaran')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Mata Pelajaran')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('code')
                        ->label('Kode Mapel')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(10)
                        ->placeholder('Contoh: BIND')
                        ->extraInputAttributes(['style' => 'text-transform: uppercase']),

                    Forms\Components\Select::make('unit_id')
                        ->label('Unit / Sekolah')
                        ->relationship('unit', 'name', modifyQueryUsing: function (Builder $query) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
                                return $query;
                            }
                            $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                            return $query->whereIn('id', $unitIds);
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('Kosongkan jika berlaku untuk semua unit di jenjang tersebut')
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $unit = \Modules\MasterData\Models\Unit::find($state);
                                if ($unit) {
                                    $set('unit_type_id', $unit->unit_type_id);
                                }
                            }
                        }),

                    Forms\Components\Select::make('unit_type_id')
                        ->label('Jenjang')
                        ->relationship('unitType', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Otomatis terisi jika sekolah dipilih')
                        ->disabled(fn (Get $get) => filled($get('unit_id')))
                        ->dehydrated(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true),
                ])->columns([
                    'sm' => 2,
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unitType.name')
                    ->label('Jenjang')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('question_banks_count')
                    ->label('Bank Soal')
                    ->counts('questionBanks')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_type_id')
                    ->label('Jenjang')
                    ->relationship('unitType', 'name'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
