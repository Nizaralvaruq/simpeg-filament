<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\QuestionBankFactory;


class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'cbt_question_banks';
    protected $guarded = [];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(\Modules\MasterData\Models\UnitType::class, 'unit_type_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    // protected static function newFactory(): QuestionBankFactory
    // {
    //     // return QuestionBankFactory::new();
    // }
}
