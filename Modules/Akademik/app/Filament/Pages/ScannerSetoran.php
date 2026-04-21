<?php

namespace Modules\Akademik\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Modules\Akademik\Models\Siswa;
use Modules\Akademik\Models\SetoranNgaji;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScannerSetoran extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected string $view = 'akademik::filament.pages.scanner-setoran';

    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?string $title = 'Scanner Setoran Ngaji';

    protected static ?string $navigationLabel = 'Scan & Input Setoran';

    protected static ?int $navigationSort = 3;

    // Scanner state
    public ?int $siswaId = null;
    public ?array $scannedUser = null;
    public ?array $riwayatTerakhir = null;

    // Stats & History
    public array $todayStats = ['total' => 0, 'countA' => 0];
    public array $recentScans = [];
    public int $volume = 70;
    public bool $isOnline = true;
    public int $pendingScans = 0;

    // Form fields
    public string $jenis_setoran = '';
    public string $nama_materi = '';
    public string $ayat_halaman = '';
    public string $predikat_nilai = '';
    public string $catatan_guru = '';
    public string $tanggal_setoran = '';

    // UI state
    public bool $showFormModal = false;

    public function mount(): void
    {
        $this->tanggal_setoran = Carbon::today()->format('Y-m-d');
        $this->loadStats();
        $this->loadRecentScans();
        $this->volume = (int)cache()->get('scanner_volume_akademik', 70);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Akademik';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-qr-code';
    }

    public function loadStats()
    {
        $today = Carbon::today();
        $total = SetoranNgaji::whereDate('tanggal_setoran', $today)->count();
        
        // Calculate simplified average or just count by grade
        $this->todayStats = [
            'total' => $total,
            'countA' => SetoranNgaji::whereDate('tanggal_setoran', $today)->where('predikat_nilai', 'A')->count(),
        ];
    }

    public function loadRecentScans()
    {
        $this->recentScans = SetoranNgaji::with('siswa')
            ->whereDate('tanggal_setoran', Carbon::today())
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->siswa->nama_lengkap,
                    'materi' => $s->nama_materi,
                    'grade' => $s->predikat_nilai,
                    'time' => Carbon::parse($s->created_at)->format('H:i'),
                ];
            })
            ->toArray();
    }

    public function processScan(string $token)
    {
        $siswa = Siswa::where('nis', $token)->where('is_active', true)->first();

        if (!$siswa) {
            $this->dispatch('scan-error', message: "Siswa dengan NIS '{$token}' tidak ditemukan.");
            return;
        }

        $this->siswaId = $siswa->id;
        $this->scannedUser = [
            'id' => $siswa->id,
            'nis' => $siswa->nis,
            'name' => $siswa->nama_lengkap,
            'kelas' => $siswa->kelas ?? '-',
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($siswa->nama_lengkap) . '&color=7F9CF5&background=EBF4FF',
        ];

        // Load last record
        $last = SetoranNgaji::where('siswa_id', $siswa->id)->latest()->first();
        $this->riwayatTerakhir = $last ? [
            'materi' => $last->nama_materi . ' (' . $last->predikat_nilai . ')',
            'tanggal' => Carbon::parse($last->tanggal_setoran)->format('d/m/Y'),
        ] : null;

        $this->resetForm();
        $this->showFormModal = true;
        
        $this->dispatch('scan-success', type: 'check-in', name: $siswa->nama_lengkap);
    }

    public function saveSetoran()
    {
        $this->validate([
            'siswaId' => 'required',
            'jenis_setoran' => 'required',
            'nama_materi' => 'required',
            'predikat_nilai' => 'required',
        ]);

        SetoranNgaji::create([
            'siswa_id'          => $this->siswaId,
            'guru_id'           => Auth::id(),
            'tanggal_setoran'   => $this->tanggal_setoran,
            'jenis_setoran'     => $this->jenis_setoran,
            'nama_materi'       => $this->nama_materi,
            'ayat_halaman'      => $this->ayat_halaman ?: null,
            'predikat_nilai'    => $this->predikat_nilai,
            'catatan_guru'      => $this->catatan_guru ?: null,
            'status_notifikasi' => false,
        ]);

        $this->showFormModal = false;
        $this->loadStats();
        $this->loadRecentScans();
        
        Notification::make()
            ->title('Setoran Berhasil Disimpan')
            ->success()
            ->send();

        $this->dispatch('setoran-saved');
    }

    protected function resetForm()
    {
        $this->jenis_setoran = '';
        $this->nama_materi = '';
        $this->ayat_halaman = '';
        $this->predikat_nilai = '';
        $this->catatan_guru = '';
    }

    public function updatedVolume($value)
    {
        cache()->forever('scanner_volume_akademik', (int)$value);
    }
}
