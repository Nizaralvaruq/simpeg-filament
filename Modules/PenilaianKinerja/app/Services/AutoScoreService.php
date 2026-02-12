<?php

namespace Modules\PenilaianKinerja\Services;

use Carbon\Carbon;
use Modules\Presensi\Models\Absensi;
use Modules\Presensi\Models\AbsensiKegiatan;
use Modules\Presensi\Models\Kegiatan;

class AutoScoreService
{
    /**
     * Konversi persentase ke skala 1-5
     */
    protected static function percentageToScore(float $percentage): float
    {
        return match (true) {
            $percentage >= 95 => 5.0,
            $percentage >= 85 => 4.0 + (($percentage - 85) / 10) * 1.0,    // 4.0 - 4.99
            $percentage >= 75 => 3.0 + (($percentage - 75) / 10) * 1.0,    // 3.0 - 3.99
            $percentage >= 60 => 2.0 + (($percentage - 60) / 15) * 1.0,    // 2.0 - 2.99
            default           => max(1.0, $percentage / 60 * 2.0),         // 1.0 - 1.99
        };
    }

    /**
     * Hitung skor kehadiran harian berdasarkan periode sesi penilaian
     *
     * Menghitung persentase kehadiran (hadir + dinas_luar) terhadap total hari kerja
     * lalu konversi ke skala 1-5
     */
    public static function getAttendanceScore(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        // Hitung hari kerja dalam periode (Mon-Fri, excl. weekends)
        $totalWorkingDays = 0;
        $current = $startDate->copy();
        $today = Carbon::today();
        $effectiveEnd = $endDate->copy()->min($today); // Jangan hitung hari ke depan

        while ($current->lte($effectiveEnd)) {
            if (Absensi::isWorkingDay($current)) {
                $totalWorkingDays++;
            }
            $current->addDay();
        }

        if ($totalWorkingDays === 0) {
            return [
                'score' => 3.0,  // Default jika belum ada hari kerja
                'percentage' => 0,
                'hadir' => 0,
                'total_working_days' => 0,
                'detail' => 'Belum ada hari kerja dalam periode',
            ];
        }

        // Hitung hari hadir (status = hadir atau dinas_luar)
        $daysPresent = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startDate->toDateString(), $effectiveEnd->toDateString()])
            ->whereIn('status', ['hadir', 'dinas_luar'])
            ->count();

        $percentage = round(($daysPresent / $totalWorkingDays) * 100, 1);
        $score = round(self::percentageToScore($percentage), 2);

        return [
            'score' => $score,
            'percentage' => $percentage,
            'hadir' => $daysPresent,
            'total_working_days' => $totalWorkingDays,
            'detail' => "{$daysPresent}/{$totalWorkingDays} hari ({$percentage}%)",
        ];
    }

    /**
     * Hitung skor partisipasi kegiatan wajib berdasarkan periode sesi penilaian
     *
     * Menghitung persentase partisipasi kegiatan wajib (is_wajib = true)
     * lalu konversi ke skala 1-5
     */
    public static function getActivityScore(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $today = Carbon::today();
        $effectiveEnd = $endDate->copy()->min($today);

        // Jumlah kegiatan wajib dalam periode
        $totalMandatoryActivities = Kegiatan::where('is_wajib', true)
            ->whereBetween('tanggal', [$startDate->toDateString(), $effectiveEnd->toDateString()])
            ->count();

        if ($totalMandatoryActivities === 0) {
            return [
                'score' => 3.0,  // Default jika tidak ada kegiatan wajib
                'percentage' => 0,
                'attended' => 0,
                'total_activities' => 0,
                'detail' => 'Tidak ada kegiatan wajib dalam periode',
            ];
        }

        // Kegiatan wajib yang diikuti user (status = 'hadir')
        $attendedActivities = AbsensiKegiatan::where('user_id', $userId)
            ->where('status', 'hadir')
            ->whereHas('kegiatan', function ($query) use ($startDate, $effectiveEnd) {
                $query->where('is_wajib', true)
                    ->whereBetween('tanggal', [$startDate->toDateString(), $effectiveEnd->toDateString()]);
            })
            ->count();

        $percentage = round(($attendedActivities / $totalMandatoryActivities) * 100, 1);
        $score = round(self::percentageToScore($percentage), 2);

        return [
            'score' => $score,
            'percentage' => $percentage,
            'attended' => $attendedActivities,
            'total_activities' => $totalMandatoryActivities,
            'detail' => "{$attendedActivities}/{$totalMandatoryActivities} kegiatan ({$percentage}%)",
        ];
    }
}
