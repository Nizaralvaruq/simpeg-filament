<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Carbon\Carbon;

class LocationAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected string $view = 'presensi::filament.pages.location-attendance-v2';

    protected static string | \UnitEnum | null $navigationGroup = 'Presensi';

    protected static ?string $title = 'Absen Dinas Luar';

    protected static ?string $navigationLabel = 'Dinas Luar';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?string $alamat_lokasi = null;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user && $user->hasRole('staff'), 403);

        $this->latitude = null;
        $this->longitude = null;
        $this->alamat_lokasi = null;

        $this->form->fill();
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasRole('staff');
    }

    /**
     * Configure the form schema for attendance input
     */
    public function form(Schema $attendFormSchema): Schema
    {
        /** @var Schema $attendFormSchema */
        $attendFormSchema->components([
            \Filament\Schemas\Components\Section::make('Informasi Kehadiran')
                ->description('Silakan lampirkan bukti foto dan keterangan tujuan dinas luar Anda.')
                ->schema([
                    FileUpload::make('foto_verifikasi')
                        ->label('Foto Selfie / Lokasi')
                        ->image()
                        ->required()
                        ->directory('absensi-verifikasi')
                        ->directory('absensi-verifikasi'),
                    Textarea::make('keterangan')
                        ->label('Tujuan / Alasan Dinas Luar')
                        ->placeholder('Contoh: Kunjungan ke Client A')
                        ->required(),
                ]),
        ])
            ->statePath('data');

        return $attendFormSchema;
    }

    public function submit()
    {
        $formData = $this->data;
        $lat = $this->latitude;
        $lng = $this->longitude;
        $foto = $formData['foto_verifikasi'] ?? null;
        $keterangan = $formData['keterangan'] ?? null;

        if (!$lat || !$lng) {
            Notification::make()
                ->title('LOKASI BELUM TERDETEKSI')
                ->body('Mohon tunggu sebentar hingga sistem mendeteksi lokasi Anda secara otomatis.')
                ->danger()
                ->send();
            return;
        }

        if (!$foto) {
            Notification::make()->title('Foto Wajib')->body('Mohon ambil foto selfie/lokasi.')->danger()->send();
            return;
        }

        if (!$keterangan) {
            Notification::make()->title('Keterangan Wajib')->body('Mohon isi tujuan dinas luar.')->danger()->send();
            return;
        }

        $user = Auth::user();
        $today = Carbon::today();

        // Check if already checked in
        $attendance = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($attendance) {
            Notification::make()->title('Gagal')->body('Anda sudah melakukan absensi hari ini.')->danger()->send();
            return;
        }

        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'status' => 'dinas_luar',
            'jam_masuk' => now(),
            'latitude' => $lat,
            'longitude' => $lng,
            'foto_verifikasi' => is_array($foto) ? reset($foto) : $foto,
            'keterangan' => $keterangan,
            'alamat_lokasi' => $this->alamat_lokasi,
        ]);

        Notification::make()
            ->title('Absensi Berhasil')
            ->body('Data absensi Dinas Luar telah disimpan.')
            ->success()
            ->send();

        return redirect()->to(MyAttendance::getUrl());
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
