<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\ExamParticipantFactory;

class ExamParticipant extends Model
{
    use HasFactory;

    protected $table = 'cbt_exam_participants';
    protected $guarded = [];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // protected static function newFactory(): ExamParticipantFactory
    // {
    //     // return ExamParticipantFactory::new();
    // }
}
