<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\SubjectFactory;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'cbt_subjects';
    protected $guarded = [];

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(\Modules\MasterData\Models\UnitType::class, 'unit_type_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(\Modules\MasterData\Models\Unit::class, 'unit_id');
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    // protected static function newFactory(): SubjectFactory
    // {
    //     // return SubjectFactory::new();
    // }
}
