<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriBarang extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}
