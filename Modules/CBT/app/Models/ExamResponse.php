<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\ExamResponseFactory;

class ExamResponse extends Model
{
    use HasFactory;

    protected $table = 'cbt_exam_responses';
    protected $guarded = [];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class);
    }

    // protected static function newFactory(): ExamResponseFactory
    // {
    //     // return ExamResponseFactory::new();
    // }
}
