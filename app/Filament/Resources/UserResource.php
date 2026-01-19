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
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\HtmlString;
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
                                // Sh 7,LLLLP.....H///'/ow employees without user OR the current employee
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
                \Filament\Actions\Action::make('view_qr')
                    ->label('ID Card QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn(User $record) => "QR Code: " . $record->name)
                    ->modalContent(function (User $record) {
                        $qrCode = $record->employee?->nip ?? null;

                        if (!$qrCode) {
                            return new HtmlString('<div class="p-4 text-center text-danger-500">User ini belum memiliki NIP/NPA di data kepegawaian.</div>');
                        }

                        // Generate QR Code using BaconQrCode
                        $renderer = new ImageRenderer(
                            new RendererStyle(200),
                            new SvgImageBackEnd()
                        );
                        $writer = new Writer($renderer);
                        $qrCodeSvg = $writer->writeString($qrCode);

                        return new HtmlString('
                        <div class="flex flex-col items-center justify-center p-4">
                            <div class="bg-white p-4 rounded-xl shadow-lg mb-4">
                                ' . $qrCodeSvg . '
                            </div>
                            <p class="text-sm text-gray-500 font-mono">' . $qrCode . '</p>
                            <p class="text-xs text-gray-400 mt-2">Gunakan QR ini pada Kartu ID Staff</p>
                        </div>
                    ');
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
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
