<?php

namespace Modules\PenilaianKinerja\Models;

use App\Models\User;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceScore extends Model
{
    protected $guarded = [];

    protected $casts = [
        'kualitas_hasil' => 'integer',
        'ketelitian' => 'integer',
        'kuantitas_hasil' => 'integer',
        'ketepatan_waktu' => 'integer',
        'kehadiran' => 'integer',
        'kepatuhan_aturan' => 'integer',
        'etika_kerja' => 'integer',
        'tanggung_jawab' => 'integer',
        'komunikasi' => 'integer',
        'kerjasama_tim' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function getAverageScoreAttribute(): float
    {
        $scores = [
            $this->kualitas_hasil,
            $this->ketelitian,
            $this->kuantitas_hasil,
            $this->ketepatan_waktu,
            $this->kehadiran,
            $this->kepatuhan_aturan,
            $this->etika_kerja,
            $this->tanggung_jawab,
            $this->komunikasi,
            $this->kerjasama_tim,
        ];

        // Filter null if any, though default is 3
        $filtered = array_filter($scores, fn($s) => !is_null($s));

        if (count($filtered) === 0) return 0;

        return array_sum($filtered) / count($filtered);
    }

    public function getGradeAttribute(): string
    {
        $avg = $this->average_score;

        return match (true) {
            $avg >= 4.51 => 'A',
            $avg >= 3.76 => 'B',
            $avg >= 3.01 => 'C',
            $avg >= 2.01 => 'D',
            default => 'E',
        };
    }

    public function getGradeLabelAttribute(): string
    {
        return match ($this->grade) {
            'A' => 'Istimewa',
            'B' => 'Sangat Baik',
            'C' => 'Baik',
            'D' => 'Cukup',
            'E' => 'Kurang',
            default => '-',
        };
    }

    public function getGradeColorAttribute(): string
    {
        return match ($this->grade) {
            'A' => 'success',
            'B' => 'info',
            'C' => 'primary',
            'D' => 'warning',
            'E' => 'danger',
            default => 'gray',
        };
    }
}
