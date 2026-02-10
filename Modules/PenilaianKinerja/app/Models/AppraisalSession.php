<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalSession extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'superior_weight' => 'integer',
        'peer_weight' => 'integer',
        'self_weight' => 'integer',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(AppraisalAssignment::class, 'session_id');
    }

    public function isActiveAndOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->startOfDay();
        return $today->between($this->start_date->startOfDay(), $this->end_date->endOfDay());
    }
}
