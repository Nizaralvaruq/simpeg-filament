<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\QuestionOptionFactory;

class QuestionOption extends Model
{
    use HasFactory;

    protected $table = 'cbt_question_options';
    protected $guarded = [];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // protected static function newFactory(): QuestionOptionFactory
    // {
    //     // return QuestionOptionFactory::new();
    // }
}
