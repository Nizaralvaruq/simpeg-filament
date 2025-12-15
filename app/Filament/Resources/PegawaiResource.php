<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use Modules\Pegawai\Models\DataInduk;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class PegawaiResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('admin_hr')) {
            return $query;
        }

        if ($user->hasRole('kepala_sekolah') || $user->hasRole('koor_jenjang')) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->toArray();

                if (empty($unitIds)) {
                    return $query->whereRaw('1=0');
                }

                return $query->whereHas('units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            return $query->whereRaw('1=0');
        }

        if ($user->hasRole('staff')) {
            if ($user->employee) {
                return $query->where('id', $user->employee->id);
            }
            return $query->whereRaw('1=0');
        }

        return $query;
    }

    protected static ?string $model = DataInduk::class;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-users';
    }

    public static function getModelLabel(): string
    {
        return 'Pegawai';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Akun Login (User)'),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP/NIY')
                            ->maxLength(255),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Kepegawaian')
                    ->schema([
                        Forms\Components\TextInput::make('jabatan')
                            ->maxLength(255),
                        Forms\Components\Select::make('status_kepegawaian')
                            ->options([
                                'Tetap' => 'Tetap',
                                'Kontrak' => 'Kontrak',
                                'Magang' => 'Magang',
                            ]),
                        Forms\Components\Select::make('units')
                            ->relationship('units', 'name')
                            ->multiple()
                            ->preload()
                            ->label('Unit Kerja'),
                        Forms\Components\Select::make('golongan_id')
                            ->relationship('golongan', 'name')
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->unique('golongans', 'name'),
                                Forms\Components\Textarea::make('description'),
                            ])
                            ->label('Golongan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')->searchable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('jabatan'),
                Tables\Columns\TextColumn::make('units.name')
                    ->badge()
                    ->separator(','),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('generate_user')
                        ->label('Buat Akun Login (Massal)')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\TextInput::make('password')
                                ->label('Password Default')
                                ->default('password')
                                ->required(),
                            Forms\Components\TextInput::make('domain')
                                ->label('Domain Email')
                                ->default('sekolah.id')
                                ->prefix('@')
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->user_id) continue;

                                $emailUsername = $record->nip ?? \Illuminate\Support\Str::slug($record->nama);
                                $email = $emailUsername . '@' . $data['domain'];

                                // Check if email exists
                                if (\App\Models\User::where('email', $email)->exists()) {
                                    $email = $emailUsername . rand(1, 999) . '@' . $data['domain'];
                                }

                                $user = \App\Models\User::create([
                                    'name' => $record->nama,
                                    'email' => $email,
                                    'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                                ]);

                                $user->assignRole('staff');
                                $record->update(['user_id' => $user->id]);
                                $count++;
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Berhasil Membuat $count Akun")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataInduks::route('/'),
            'create' => Pages\CreateDataInduk::route('/create'),
            'edit' => Pages\EditDataInduk::route('/{record}/edit'),
        ];
    }
}
