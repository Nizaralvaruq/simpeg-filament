<?php

namespace Modules\Kepegawaian\Filament\Resources;

use Modules\Kepegawaian\Filament\Resources\PegawaiResource\Pages;
use Modules\Kepegawaian\Models\DataInduk;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;

use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class PegawaiResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Super Admin & Admin HR: View ALL
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // 2. Kepala Sekolah & Koor Jenjang: View Unit Employees
        // Limitation: Currently Koor Jenjang must be assigned to the units they oversee.
        if ($user->hasRole('kepala_sekolah') || $user->hasRole('koor_jenjang')) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->toArray();
                return $query->whereHas('units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
            return $query->whereRaw('1=0');
        }

        // 3. Staff: View Self Only
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
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Lengkap'),
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP/NIY')
                            ->maxLength(255),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Buat Akun Login (Opsional)')
                    ->description('Isi jika ingin sekaligus membuatkan akun login untuk pegawai ini.')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email Login')
                            ->email()
                            ->unique('users', 'email')
                            ->dehydrated(false), // Do not save to data_induk table
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrated(false)
                            ->requiredWith('email'),
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
            ->headerActions([
                \Filament\Actions\Action::make('template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PegawaiTemplateExport, 'template_pegawai.xlsx');
                    }),
                \Filament\Actions\Action::make('import')
                    ->label('Import Pegawai')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->form([
                        Forms\Components\FileUpload::make('attachment')
                            ->label('File Excel (.xlsx)')
                            ->disk('public')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($data['attachment']);

                            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PegawaiImport, $filePath);

                            \Filament\Notifications\Notification::make()
                                ->title('Import Berhasil')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Import Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
