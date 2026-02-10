<?php

namespace Modules\MasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Kepegawaian\Models\DataInduk;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius' => 'integer',
    ];

    public function employees()
    {
        return $this->belongsToMany(DataInduk::class, 'employee_unit', 'unit_id', 'employee_id');
    }
}
