<?php

namespace Modules\PenilaianKinerja\Livewire;

use Livewire\Component;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\PerformanceScore;
use Illuminate\Support\Facades\Auth;

class PenilaianStats extends Component
{
    public int    $totalSesi       = 0;
    public int    $sesiAktif       = 0;
    public int    $tugasPending    = 0;
    public int    $tugasSelesai    = 0;
    public int    $tugasExpired    = 0;
    public ?string $namaSesiAktif  = null;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit']);

        // Statistik Sesi
        $this->totalSesi = AppraisalSession::count();
        $sesiAktif = AppraisalSession::where('is_active', true)
            ->where('status', '!=', 'Closed')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();
        $this->sesiAktif = $sesiAktif ? 1 : 0;
        $this->namaSesiAktif = $sesiAktif?->name ?? $sesiAktif?->title ?? null;

        // Statistik Penugasan
        $assignmentQuery = AppraisalAssignment::query();
        if (!$isAdmin) {
            $assignmentQuery->where('rater_id', $user->id);
        }

        $this->tugasPending  = (clone $assignmentQuery)->where('status', 'pending')->count();
        $this->tugasSelesai  = (clone $assignmentQuery)->where('status', 'completed')->count();
        $this->tugasExpired  = (clone $assignmentQuery)->where('status', 'expired')->count();
    }

    public function render()
    {
        return view('penilaiankinerja::livewire.penilaian-stats');
    }
}
