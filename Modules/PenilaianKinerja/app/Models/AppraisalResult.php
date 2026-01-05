<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppraisalResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'score' => 'integer',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(AppraisalAssignment::class, 'assignment_id');
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(AppraisalIndicator::class, 'indicator_id');
    }
}
