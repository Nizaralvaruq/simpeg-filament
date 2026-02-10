<?php

namespace Modules\Presensi\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Modules\Presensi\Models\Absensi;
use Modules\Presensi\Models\JadwalPiket;
use Modules\Presensi\Models\Kegiatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class QrScanner extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-qr-code';

    protected string $view = 'presensi::filament.pages.qr-scanner';

    protected static string | \UnitEnum | null $navigationGroup = 'Presensi';

    protected static ?string $title = 'Scanner Dashboard';

    protected static ?string $navigationLabel = 'Scan QR Kehadiran';

    public ?string $lastScannedToken = null;
    public ?array $scannedUser = null;

    // Real-time stats
    public array $todayStats = ['checkedIn' => 0, 'checkedOut' => 0];
    public array $recentScans = [];

    // Scanner controls
    public int $volume = 70;

    // Event Mode
    public string $scanMode = 'daily'; // 'daily' or 'event'
    public ?int $selectedEventId = null;

    // Connection status
    public bool $isOnline = true;
    public int $pendingScans = 0;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user has permanent access OR is on piket duty today
        $hasAccess = $user && (
            $user->hasAnyRole(['super_admin', 'admin_unit']) ||
            JadwalPiket::isUserOnPiketToday($user->id)
        );

        if (!$hasAccess) {
            abort(403, 'Akses Ditolak: Hanya petugas piket yang dijadwalkan hari ini yang dapat menggunakan scanner.');
        }

        // Initialize stats
        $this->loadTodayStats();
        $this->loadRecentScans();

        // Load Global States
        $this->volume = (int)cache()->get('scanner_volume', 70);
    }

    /**
     * Check if navigation should be visible
     */
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) return false;

        return $user->hasAnyRole(['super_admin', 'admin_unit']) ||
            JadwalPiket::isUserOnPiketToday($user->id);
    }

    /**
     * Get navigation badge
     */
    public static function getNavigationBadge(): ?string
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) return null;

        if (JadwalPiket::isUserOnPiketToday($user->id)) {
            return 'Piket Hari Ini';
        }

        return null;
    }

    /**
     * Get navigation badge color
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public function getEventsProperty()
    {
        return Kegiatan::whereDate('tanggal', Carbon::today())
            ->where('is_closed', false)
            ->get()
            ->pluck('nama_kegiatan', 'id');
    }

    public function updatedScanMode()
    {
        $this->selectedEventId = null;
        $this->loadTodayStats(); // Refresh stats for mode
    }

    public function processScan(string $token, ?float $lat = null, ?float $lng = null, bool $isSilent = false)
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        $this->lastScannedToken = $token;

        // Cari user berdasarkan qr_token ATAU NIP
        $user = User::where('qr_token', $token)
            ->orWhereHas('employee', function ($query) use ($token) {
                $query->where('nip', $token);
            })->first();

        if (!$user) {
            if (!$isSilent) {
                $this->dispatch('scan-error', message: 'NIP/Token tidak valid!');
                Notification::make()
                    ->title('Gagal')
                    ->body('QR Code (NIP) tidak terdaftar.')
                    ->danger()
                    ->send();
            }
            return;
        }

        // --- BRANCH: EVENT MODE ---
        if ($this->scanMode === 'event') {
            $this->recordEventAttendance($user, $lat, $lng, $isSilent);
            return;
        }

        // --- GEO-TAGGING VALIDATION (Unit-Aware) ---
        $globalSettings = \Modules\MasterData\Models\Setting::get();

        // 1. Get Employee Units with Location Data
        $userUnits = $user->employee?->units()->whereNotNull('latitude')->whereNotNull('longitude')->get();

        $validGeofence = null;

        if ($userUnits && $userUnits->isNotEmpty()) {
            // Check if user is within ANY of their units' geofences
            foreach ($userUnits as $unit) {
                $dist = $this->calculateDistance($lat, $lng, (float)$unit->latitude, (float)$unit->longitude);
                $radius = (int) ($unit->radius ?? $globalSettings->office_radius ?? 100);

                if ($dist <= $radius) {
                    $validGeofence = [
                        'name' => $unit->name,
                        'distance' => $dist,
                        'radius' => $radius
                    ];
                    break;
                }
            }

            // If not in any unit geofence, we fail (or we could fallback to global, but unit-specific usually overrides)
            if (!$validGeofence) {
                if (!$isSilent) {
                    $this->dispatch('scan-error', message: 'Diluar Lokasi Unit!');
                    Notification::make()
                        ->title('Gagal: Diluar Lokasi Unit')
                        ->body("Anda tidak berada di area unit kerja Anda.")
                        ->danger()
                        ->send();
                }
                return;
            }
        } else {
            // 2. Fallback to Global Settings
            $officeLat = (float) $globalSettings->office_latitude;
            $officeLng = (float) $globalSettings->office_longitude;
            $maxRadiusMeters = (int) ($globalSettings->office_radius ?? 100);

            if ($officeLat && $officeLng) {
                if (!$lat || !$lng) {
                    if (!$isSilent) {
                        $this->dispatch('scan-error', message: 'Lokasi Wajib Diaktifkan!');
                        Notification::make()
                            ->title('Akses Ditolak')
                            ->body('Browser Anda tidak mengirimkan data lokasi. Mohon izinkan akses lokasi (GPS).')
                            ->danger()
                            ->send();
                    }
                    return;
                }

                $distance = $this->calculateDistance($lat, $lng, $officeLat, $officeLng);
                if ($distance > $maxRadiusMeters) {
                    if (!$isSilent) {
                        $this->dispatch('scan-error', message: "Lokasi terlalu jauh! ({$distance}m)");
                        Notification::make()
                            ->title('Gagal: Diluar Lokasi')
                            ->body("Anda berada {$distance}m dari pusat. Batas maksimal adalah {$maxRadiusMeters}m.")
                            ->danger()
                            ->send();
                    }
                    return;
                }
            }
        }

        $this->scannedUser = [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->getFilamentAvatarUrl(),
        ];

        $today = Carbon::today();
        $attendance = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        try {
            if (!$attendance) {
                // Check-in
                Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $today,
                    'status' => 'hadir',
                    'jam_masuk' => now(),
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);

                if (!$isSilent) {
                    $this->dispatch(
                        'scan-success',
                        type: 'check-in',
                        name: $user->name,
                        email: $user->email,
                        avatar: $user->getFilamentAvatarUrl()
                    );
                    Notification::make()
                        ->title('Check-in Berhasil')
                        ->body("Staff: {$user->name} sudah masuk.")
                        ->success()
                        ->send();
                }

                // Refresh stats
                $this->loadTodayStats();
                $this->loadRecentScans();
            } elseif ($attendance->jam_masuk && !$attendance->jam_keluar) {
                // Check-out
                if (!$isSilent && Carbon::parse($attendance->jam_masuk)->diffInMinutes(now()) < 1) {
                    $this->dispatch('scan-error', message: 'Tunggu 1 menit sebelum scan keluar.');
                    return;
                }

                $attendance->update([
                    'jam_keluar' => now(),
                ]);

                if (!$isSilent) {
                    $this->dispatch(
                        'scan-success',
                        type: 'check-out',
                        name: $user->name,
                        email: $user->email,
                        avatar: $user->getFilamentAvatarUrl()
                    );
                    Notification::make()
                        ->title('Check-out Berhasil')
                        ->body("Staff: {$user->name} sudah pulang.")
                        ->info()
                        ->send();
                }

                // Refresh stats
                $this->loadTodayStats();
                $this->loadRecentScans();
            } else {
                if (!$isSilent) {
                    $this->dispatch('scan-error', message: 'Anda sudah absen hari ini.');
                    Notification::make()
                        ->title('Sudah Absen')
                        ->body("Staff: {$user->name} sudah melakukan check-in dan check-out.")
                        ->warning()
                        ->send();
                }
            }
        } catch (\Exception $e) {
            if (!$isSilent) {
                $this->dispatch('scan-error', message: 'Terjadi kesalahan sistem.');
            }
        }
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $meters = $miles * 1609.344;

        return round($meters);
    }

    /**
     * Consolidated refresh for real-time polling
     */
    public function refreshScannerData()
    {
        $this->loadTodayStats();
        $this->loadRecentScans();
    }

    /**
     * Load today's statistics
     */
    public function loadTodayStats()
    {
        $today = Carbon::today();

        $checkedIn = Absensi::whereDate('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->count();

        $checkedOut = Absensi::whereDate('tanggal', $today)
            ->whereNotNull('jam_keluar')
            ->count();

        $this->todayStats = [
            'checkedIn' => $checkedIn,
            'checkedOut' => $checkedOut
        ];
    }

    /**
     * Load recent scans (last 10)
     */
    public function loadRecentScans()
    {
        $today = Carbon::today();

        $scans = Absensi::with('user')
            ->whereDate('tanggal', $today)
            ->latest('updated_at')
            ->limit(10)
            ->get()
            ->map(function ($absensi) {
                return [
                    'name' => $absensi->user->name ?? 'Unknown',
                    'email' => $absensi->user->email ?? '',
                    'avatar' => $absensi->user ? $absensi->user->getFilamentAvatarUrl() : null,
                    'type' => $absensi->jam_keluar ? 'check-out' : 'check-in',
                    'time' => $absensi->jam_keluar ?
                        Carbon::parse($absensi->jam_keluar)->format('H:i') :
                        Carbon::parse($absensi->jam_masuk)->format('H:i'),
                    'timestamp' => $absensi->updated_at->diffForHumans()
                ];
            })
            ->toArray();

        $this->recentScans = $scans;
    }


    /**
     * Update volume setting
     */
    public function updatedVolume($value)
    {
        $this->volume = max(0, min(100, (int)$value));
        cache()->forever('scanner_volume', $this->volume);
    }

    /**
     * Sync offline scans
     */
    public function syncOfflineScans(array $scans)
    {
        foreach ($scans as $scan) {
            $token = $scan['token'];
            $lat = $scan['lat'] ?? null;
            $lng = $scan['lng'] ?? null;

            // Note: We don't dispatch sound/modal for background sync
            // to avoid overwhelming the UI
            $this->processScan($token, $lat, $lng, isSilent: true);
        }

        Notification::make()
            ->title('Sync Berhasil')
            ->body(count($scans) . ' data kehadiran offline telah disinkronkan.')
            ->success()
            ->send();

        $this->loadTodayStats();
        $this->loadRecentScans();
    }

    protected function recordEventAttendance(User $user, ?float $lat, ?float $lng, bool $isSilent)
    {
        if (!$this->selectedEventId) {
            if (!$isSilent) $this->dispatch('scan-error', message: 'Pilih Event terlebih dahulu!');
            return;
        }

        $kegiatan = Kegiatan::find($this->selectedEventId);
        if (!$kegiatan || $kegiatan->is_closed) {
            if (!$isSilent) $this->dispatch('scan-error', message: 'Event tidak valid atau sudah ditutup.');
            return;
        }

        // Check duplicated
        $exists = $kegiatan->absensiKegiatans()->where('user_id', $user->id)->exists();
        if ($exists) {
            if (!$isSilent) {
                $this->dispatch('scan-error', message: 'Sudah absen di event ini.');
                Notification::make()
                    ->title('Sudah Hadir')
                    ->body("{$user->name} sudah tercatat hadir di {$kegiatan->nama_kegiatan}.")
                    ->warning()
                    ->send();
            }
            return;
        }

        $kegiatan->absensiKegiatans()->create([
            'user_id' => $user->id,
            'jam_absen' => now(),
            'status' => 'hadir',
        ]);

        if (!$isSilent) {
            $this->dispatch(
                'scan-success',
                type: 'check-in',
                name: $user->name,
                email: $user->email,
                avatar: $user->getFilamentAvatarUrl()
            );

            Notification::make()
                ->title('Hadir di Event')
                ->body("{$user->name} berhasil check-in di {$kegiatan->nama_kegiatan}.")
                ->success()
                ->send();
        }

        // Refresh local stats if needed (though main stats are daily)
        $this->loadRecentScans();
    }
}
