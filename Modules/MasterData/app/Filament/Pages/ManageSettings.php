<?php

namespace Modules\MasterData\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Modules\MasterData\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Schema;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'masterdata::filament.pages.manage-settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Pengaturan Global';

    protected static ?string $title = 'Pengaturan Global';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public function mount(): void
    {
        $settings = Setting::get();
        $this->data = $settings->toArray();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Umum')
                    ->description('Pengaturan dasar aplikasi.')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('app_name')
                            ->label('Nama Aplikasi')
                            ->required(),
                    ]),

                \Filament\Schemas\Components\Section::make('Waktu Kerja')
                    ->description('Tentukan jam operasional untuk validasi absensi.')
                    ->schema([
                        \Filament\Forms\Components\TimePicker::make('office_start_time')
                            ->label('Jam Masuk')
                            ->required(),
                        \Filament\Forms\Components\TimePicker::make('office_end_time')
                            ->label('Jam Pulang')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('late_tolerance')
                            ->label('Toleransi Keterlambatan (Menit)')
                            ->numeric()
                            ->suffix('Menit')
                            ->default(0)
                            ->required(),
                    ])->columns(3),

                \Filament\Schemas\Components\Section::make('Lokasi Kantor (Geofencing)')
                    ->description('Tentukan titik koordinat kantor untuk validasi lokasi scan.')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('office_latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->placeholder('-6.2088'),
                                \Filament\Forms\Components\TextInput::make('office_longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->placeholder('106.8456'),
                            ]),
                        \Filament\Forms\Components\TextInput::make('office_radius')
                            ->label('Radius Maksimal (Meter)')
                            ->numeric()
                            ->suffix('Meter')
                            ->default(100)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = Setting::get();
        $settings->update($this->data);

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->success()
            ->send();
    }
}
