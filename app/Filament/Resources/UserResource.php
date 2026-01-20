<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;


class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 15;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Master';
    }

    public static function getModelLabel(): string
    {
        return 'User';
    }
    public static function getPluralModelLabel(): string
    {
        return 'User';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->hasAnyRole(['super_admin', 'admin_unit']) ?? false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1=0');
        }

        // Super admin sees all users
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Admin unit sees users from their unit(s)
        if ($user->hasRole('admin_unit')) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->all();

                return $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            return $query->whereRaw('1=0');
        }

        // Others cannot access
        return $query->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Akun')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Pilih Pegawai (Data Induk)')
                            ->options(function ($record) {
                                // Show employees without user OR the current employee
                                $query = \Modules\Kepegawaian\Models\DataInduk::query()
                                    ->where(function ($q) use ($record) {
                                        $q->whereNull('user_id');
                                        if ($record && $record->employee) {
                                            $q->orWhere('id', $record->employee->id);
                                        }
                                    });
                                return $query->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                if ($state) {
                                    $employee = \Modules\Kepegawaian\Models\DataInduk::find($state);
                                    if ($employee) {
                                        $set('name', $employee->nama);
                                        $set('email', $employee->user->email ?? '');
                                    }
                                }
                            })
                            ->default(fn($record) => $record?->employee?->id)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Lengkap'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => \Illuminate\Support\Facades\Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Role (Akses)'),

                        // NIP Field from Employee (Read-only view)
                        Forms\Components\TextInput::make('employee.nip')
                            ->label('NIP / NPA (QR Code Source)')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($record) => $record?->employee?->nip !== null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->separator(','),

                Tables\Columns\TextColumn::make('employee.jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('employee.units.name')
                    ->label('Unit')
                    ->badge()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
