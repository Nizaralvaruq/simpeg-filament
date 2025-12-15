<?php

namespace Modules\MasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Golongan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(DataInduk::class, 'golongan_id');
    }
}
