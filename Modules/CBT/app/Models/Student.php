<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CBT\Database\Factories\StudentFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $table = 'cbt_students';
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(\Modules\MasterData\Models\UnitType::class, 'unit_type_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(\Modules\MasterData\Models\Unit::class, 'unit_id');
    }
}
