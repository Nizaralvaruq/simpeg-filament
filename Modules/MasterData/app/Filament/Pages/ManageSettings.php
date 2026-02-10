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
                    ->description('Pengaturan dasar identitas aplikasi dan hari kerja.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('app_name')
                            ->label('Nama Aplikasi')
                            ->placeholder('Contoh: SIMPEG IHYA')
                            ->required(),
                        \Filament\Forms\Components\CheckboxList::make('working_days')
                            ->label('Hari Kerja Aktif')
                            ->options([
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                                7 => 'Minggu',
                            ])
                            ->columns([
                                'default' => 2,
                                'sm' => 3,
                                'md' => 4,
                                'lg' => 7,
                            ])
                            ->required(),
                    ]),

                \Filament\Schemas\Components\Section::make('Waktu & Toleransi')
                    ->description('Tentukan jam operasional dan batas keterlambatan.')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        \Filament\Forms\Components\TimePicker::make('office_start_time')
                            ->label('Jam Masuk Kantor')
                            ->seconds(false)
                            ->required(),
                        \Filament\Forms\Components\TimePicker::make('auto_alpha_time')
                            ->label('Batas Akhir Absen (Auto-Alpha)')
                            ->helperText('Pegawai yang belum melakukan scan masuk setelah jam ini akan otomatis dianggap Alpha oleh sistem.')
                            ->seconds(false)
                            ->after('office_start_time')
                            ->required(),
                        \Filament\Forms\Components\TimePicker::make('office_end_time')
                            ->label('Jam Pulang Kantor')
                            ->seconds(false)
                            ->after('office_start_time')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('late_tolerance')
                            ->label('Toleransi Keterlambatan')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('Menit')
                            ->helperText('Waktu tambahan yang diberikan sebelum pegawai dicatat terlambat.')
                            ->default(0)
                            ->required(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Keamanan Lokasi (Geofencing)')
                    ->description('Tentukan koordinat pusat kantor dan radius jangkauan absen.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('office_latitude')
                                    ->label('Latitude Kantor')
                                    ->numeric()
                                    ->extraInputAttributes(['step' => 'any'])
                                    ->id('office-latitude')
                                    ->placeholder('-6.2088')
                                    ->suffixActions([
                                        Action::make('getLocation')
                                            ->icon('heroicon-m-map-pin')
                                            ->color('warning')
                                            ->tooltip('Ambil lokasi dari device INI (Laptop/HP). Pastikan Anda sedang berada di kantor!')
                                            ->action(fn() => $this->dispatch('get-current-location')),
                                        Action::make('openMap')
                                            ->icon('heroicon-m-globe-alt')
                                            ->color('info')
                                            ->tooltip('Lihat di Google Maps')
                                            ->url(fn($get) => $get('office_latitude') && $get('office_longitude')
                                                ? 'https://www.google.com/maps/search/?api=1&query=' . $get('office_latitude') . ',' . $get('office_longitude')
                                                : null)
                                            ->openUrlInNewTab()
                                            ->visible(fn($get) => $get('office_latitude') && $get('office_longitude')),
                                    ]),
                                \Filament\Forms\Components\TextInput::make('office_longitude')
                                    ->label('Longitude Kantor')
                                    ->id('office-longitude')
                                    ->extraInputAttributes(['step' => 'any'])
                                    ->numeric()
                                    ->placeholder('106.8456'),
                            ]),
                        \Filament\Forms\Components\TextInput::make('office_radius')
                            ->label('Radius Jangkau Maksimal')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('Meter')
                            ->helperText('Jarak maksimal (dalam meter) pegawai diperbolehkan melakukan scan dari titik koordinat kantor.')
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
