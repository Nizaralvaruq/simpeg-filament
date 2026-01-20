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
use Filament\Actions\Action;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'masterdata::filament.pages.manage-settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Presensi';

    protected static ?string $navigationLabel = 'Pengaturan Absensi';

    protected static ?string $title = 'Pengaturan Global';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action('save')
                ->color('primary'),
        ];
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
                        \Filament\Forms\Components\CheckboxList::make('working_days')
                            ->label('Hari Kerja')
                            ->options([
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                                7 => 'Minggu',
                            ])
                            ->columns(4)
                            ->required(),
                    ]),

                \Filament\Schemas\Components\Section::make('Waktu Kerja')
                    ->description('Tentukan jam operasional untuk validasi absensi.')
                    ->schema([
                        \Filament\Forms\Components\TimePicker::make('office_start_time')
                            ->label('Jam Masuk')
                            ->required(),
                        \Filament\Forms\Components\TimePicker::make('auto_alpha_time')
                            ->label('Batas Waktu Absen (Auto-Alpha)')
                            ->helperText('Setelah jam ini, pegawai yang belum absen akan dianggap Alpha.')
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
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Lokasi Kantor (Geofencing)')
                    ->description('Tentukan titik koordinat kantor untuk validasi lokasi scan.')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('office_latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->id('office-latitude')
                                    ->placeholder('-6.2088')
                                    ->suffixAction(
                                        Action::make('getLocation')
                                            ->icon('heroicon-m-map-pin')
                                            ->label('Ambil Lokasi')
                                            ->action(fn() => $this->dispatch('get-current-location'))
                                    ),
                                \Filament\Forms\Components\TextInput::make('office_longitude')
                                    ->label('Longitude')
                                    ->id('office-longitude')
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
