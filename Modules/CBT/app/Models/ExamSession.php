<?php

namespace Modules\CBT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CBT\Database\Factories\ExamSessionFactory;

class ExamSession extends Model
{
    use HasFactory;

    protected $table = 'cbt_exam_sessions';
    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'force_closed' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ExamResponse::class);
    }

    // protected static function newFactory(): ExamSessionFactory
    // {
    //     // return ExamSessionFactory::new();
    // }
}
