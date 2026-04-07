<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\MasterData\Models\Unit;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    protected $guarded = [];

    protected $casts = [
        'tanggal_pinjam'     => 'date',
        'rencana_kembali'    => 'date',
        'tanggal_kembali'    => 'datetime',
        'approved_at'        => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->nomor_peminjaman)) {
                $model->nomor_peminjaman = self::generateNomor();
            }
        });
    }

    public static function generateNomor(): string
    {
        $bulan = strtoupper(now()->locale('id')->isoFormat('MMM'));
        $tahun = now()->year;

        $prefix = "PIN/{$bulan}/{$tahun}/";

        $last = self::where('nomor_peminjaman', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('nomor_peminjaman')
            ->first();

        $urut = 1;
        if ($last) {
            $lastUrut = (int) substr($last->nomor_peminjaman, strrpos($last->nomor_peminjaman, '/') + 1);
            $urut = $lastUrut + 1;
        }

        return $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(PeminjamanDetail::class, 'peminjaman_id');
    }
}
