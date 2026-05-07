<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Modules\Presensi\Models\Kegiatan;
use Modules\Presensi\Models\AbsensiKegiatan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SelfScanKegiatan extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-camera';

    protected string $view = 'presensi::filament.pages.self-scan-kegiatan';

    protected static string|\UnitEnum|null $navigationGroup = 'Presensi';

    protected static ?string $title = 'Scan Kehadiran';

    protected static ?string $navigationLabel = 'Scan Kehadiran';

    protected static ?int $navigationSort = 1;

    // Scan state
    public bool $scanSuccess = false;
    public bool $scanError = false;
    public string $message = '';
    public ?array $lastScanResult = null;

    // Today's attendance history for current user
    public array $riwayatHariIni = [];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    public function mount(): void
    {
        $this->loadRiwayat();
    }

    public function loadRiwayat(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->riwayatHariIni = AbsensiKegiatan::with('kegiatan')
            ->where('user_id', $user->id)
            ->whereHas('kegiatan', function ($q) {
                $q->whereDate('tanggal', Carbon::today());
            })
            ->latest()
            ->get()
            ->map(fn($abs) => [
                'kegiatan_nama' => $abs->kegiatan->nama_kegiatan ?? '-',
                'jam_absen'     => $abs->jam_absen ? Carbon::parse($abs->jam_absen)->format('H:i') : '-',
                'status'        => $abs->status,
                'metode_scan'   => $abs->metode_scan,
            ])
            ->toArray();
    }

    /**
     * Called by the front-end JS after QR is decoded.
     * Token format: KEGIATAN-{id}
     */
    public function processScan(string $token): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->scanSuccess = false;
        $this->scanError   = false;
        $this->message     = '';
        $this->lastScanResult = null;

        // 1. Validate token format
        if (!preg_match('/^KEGIATAN-(\d+)$/', $token, $matches)) {
            $this->scanError = true;
            $this->message   = 'QR Code tidak valid untuk kegiatan.';
            $this->dispatch('self-scan-error', message: $this->message);
            return;
        }

        $kegiatanId = (int) $matches[1];

        // 2. Find kegiatan
        /** @var Kegiatan|null $kegiatan */
        $kegiatan = Kegiatan::find($kegiatanId);
        if (!$kegiatan) {
            $this->scanError = true;
            $this->message   = 'Kegiatan tidak ditemukan.';
            $this->dispatch('self-scan-error', message: $this->message);
            return;
        }

        // 3. Check if kegiatan is closed
        if ($kegiatan->getAttribute('is_closed')) {
            $this->scanError = true;
            $this->message   = "Kegiatan \"{$kegiatan->getAttribute('nama_kegiatan')}\" sudah ditutup.";
            $this->dispatch('self-scan-error', message: $this->message);
            return;
        }

        // 4. Check duplicate
        $exists = AbsensiKegiatan::where('kegiatan_id', $kegiatanId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            $this->scanError = true;
            $this->message   = "Anda sudah tercatat hadir di kegiatan \"{$kegiatan->getAttribute('nama_kegiatan')}\".";
            $this->dispatch('self-scan-error', message: $this->message);
            return;
        }

        // 5. Save attendance
        AbsensiKegiatan::create([
            'kegiatan_id'  => $kegiatanId,
            'user_id'      => $user->id,
            'jam_absen'    => now(),
            'status'       => 'hadir',
            'metode_scan'  => 'self',
            'keterangan'   => "Scan Kehadiran oleh {$user->name}",
        ]);

        $this->scanSuccess = true;
        $kegiatanNama      = $kegiatan->getAttribute('nama_kegiatan');
        $this->message     = "Berhasil! Anda tercatat hadir di kegiatan \"{$kegiatanNama}\".";
        $this->lastScanResult = [
            'kegiatan' => $kegiatanNama,
            'waktu'    => now()->format('H:i'),
            'tanggal'  => Carbon::parse($kegiatan->getAttribute('tanggal'))->translatedFormat('d F Y'),
            'lokasi'   => $kegiatan->getAttribute('lokasi') ?? '-',
        ];

        $this->loadRiwayat();

        Notification::make()
            ->title('Absensi Berhasil!')
            ->body("Anda berhasil absen di kegiatan \"{$kegiatanNama}\".")
            ->success()
            ->send();

        $this->dispatch('self-scan-success', result: $this->lastScanResult);
    }
}
