<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\ExamFactory;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'cbt_exams';
    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(\Modules\MasterData\Models\UnitType::class, 'unit_type_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(\Modules\MasterData\Models\Unit::class, 'unit_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ExamParticipant::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    // protected static function newFactory(): ExamFactory
    // {
    //     // return ExamFactory::new();
    // }
}
