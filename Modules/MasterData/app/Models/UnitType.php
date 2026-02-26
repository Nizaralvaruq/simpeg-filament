<?php

namespace Modules\MasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function units()
    {
        return $this->hasMany(Unit::class, 'unit_type_id');
    }
}
