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
        $user = Auth::user();
        return $user?->hasAnyRole([ 'super_admin']) ?? false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = Auth::user();

        if (! $user?->hasAnyRole([ 'super_admin'])) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Akun')
                    ->schema([
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
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'nama')
                            ->searchable()
                            ->preload()
                            ->label('Terhubung ke Pegawai')
                            ->placeholder('Pilih pegawai (opsional)')
                            ->helperText('Optional: Hubungkan user ini dengan data pegawai'),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Role (Akses)'),
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
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Terhubung ke Pegawai')
                    ->placeholder('Belum Terhubung')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
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
