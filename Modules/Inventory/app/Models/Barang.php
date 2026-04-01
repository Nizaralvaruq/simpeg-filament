<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterData\Models\Unit;

class Barang extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'barang_id');
    }

    public function permintaanDetails()
    {
        return $this->hasMany(PermintaanBarangDetail::class, 'barang_id');
    }
}
