<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class StockTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
