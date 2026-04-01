<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\QuestionFactory;

class Question extends Model
{
    use HasFactory;

    protected $table = 'cbt_questions';
    protected $guarded = [];

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    // protected static function newFactory(): QuestionFactory
    // {
    //     // return QuestionFactory::new();
    // }
}
