<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermintaanBarangDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function permintaanBarang()
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_barang_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
