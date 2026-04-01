<?php

namespace Modules\CBT\Filament\Resources;

use Modules\CBT\Models\Student;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;
use Modules\CBT\Filament\Resources\StudentResource\Pages;
use Filament\Support\Enums\Width;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CBT / Ujian';
    }

    public static function getModelLabel(): string
    {
        return 'Siswa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Siswa';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || $user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            return $query;
        }

        if ($user->hasAnyRole(['admin_unit', 'kepala_sekolah', 'koor_jenjang'])) {
            $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
            return $query->whereIn('unit_id', $unitIds);
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Informasi Akun (User)')
                ->description('Data ini digunakan untuk login siswa.')
                ->icon('heroicon-o-lock-closed')
                ->schema([
                    Forms\Components\TextInput::make('user_name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->autocomplete('off')
                        ->formatStateUsing(fn ($record) => $record?->user?->name),

                    Forms\Components\TextInput::make('user_email')
                        ->label('Email (Login)')
                        ->email()
                        ->required()
                        ->autocomplete('new-password')
                        ->helperText('Otomatis terisi dari NISN saat ditambah, atau bisa diubah manual.')
                        ->unique(
                            table: 'users', 
                            column: 'email', 
                            ignoreRecord: true,
                            modifyRuleUsing: function (Unique $rule, ?Student $record) {
                                return $rule->ignore($record ? $record->user_id : null);
                            }
                        )
                        ->formatStateUsing(fn ($record) => $record?->user?->email),

                    Forms\Components\TextInput::make('user_password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->autocomplete('new-password')
                        ->required(fn ($context) => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->helperText('Otomatis terisi sama dengan NISN saat baru ditambahkan. Kosongkan jika tidak ingin mengubah password pada saat edit.'),
                ])->columns(3),

            \Filament\Schemas\Components\Section::make('Informasi Akademik')
                ->description('Data spesifik siswa untuk keperluan ujian.')
                ->icon('heroicon-o-academic-cap')
                ->schema([
                    Forms\Components\TextInput::make('nisn')
                        ->label('NISN')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (Set $set, Get $get, $state, $context) {
                            if ($context === 'create' && filled($state)) {
                                // Selalu update email jika belum diisi manual atau masih menggunakan format default
                                $currentEmail = $get('user_email');
                                if (empty($currentEmail) || str_ends_with($currentEmail, '@sekolah.com')) {
                                    $set('user_email', "{$state}@sekolah.com");
                                }
                                
                                // Selalu update password jika belum diisi manual
                                if (empty($get('user_password'))) {
                                    $set('user_password', $state);
                                }
                            }
                        }),

                    Forms\Components\TextInput::make('batch_year')
                        ->label('Angkatan/Tahun Ajaran')
                        ->placeholder('Contoh: 2023/2024')
                        ->maxLength(20),

                    Forms\Components\TextInput::make('major')
                        ->label('Jurusan')
                        ->placeholder('Contoh: IPA / IPS')
                        ->maxLength(50)
                        ->visible(fn (Get $get) => in_array(
                            \Modules\MasterData\Models\UnitType::find($get('unit_type_id'))?->name, 
                            ['SMA', 'SMK']
                        )),

                    Forms\Components\TextInput::make('class_name')
                        ->label('Kelas')
                        ->placeholder('Contoh: 12 IPA 1')
                        ->required()
                        ->maxLength(50),

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
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $unit = \Modules\MasterData\Models\Unit::find($state);
                                if ($unit) {
                                    $set('unit_type_id', $unit->unit_type_id);
                                }
                            } else {
                                $set('unit_type_id', null);
                            }
                        }),

                    Forms\Components\Select::make('unit_type_id')
                        ->label('Jenjang')
                        ->relationship('unitType', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled()
                        ->dehydrated(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('class_name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('major')
                    ->label('Jurusan')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('batch_year')
                    ->label('Angkatan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unitType.name')
                    ->label('Jenjang')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Sekolah')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_type_id')
                    ->label('Filter Jenjang')
                    ->relationship('unitType', 'name'),

                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Filter Sekolah')
                    ->relationship('unit', 'name', modifyQueryUsing: function (Builder $query) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        if ($user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
                            return $query;
                        }
                        $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                        return $query->whereIn('id', $unitIds);
                    }),

                Tables\Filters\SelectFilter::make('class_name')
                    ->label('Filter Kelas')
                    ->options(fn () => Student::distinct()->pluck('class_name', 'class_name')->toArray()),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->after(function (Student $record) {
                        $record->user?->delete();
                    }),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->after(function (\Illuminate\Database\Eloquent\Collection $records) {
                            foreach ($records as $record) {
                                $record->user?->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
