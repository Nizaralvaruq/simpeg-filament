<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalSession extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::updated(function ($session) {
            // Trigger auto-archive if session is deactivated OR status changed to Closed
            if (
                ($session->wasChanged('is_active') && !$session->is_active) ||
                ($session->wasChanged('status') && $session->status === 'Closed')
            ) {
                $session->expirePendingAssignments();
            }
        });
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'superior_weight' => 'integer',
        'peer_weight' => 'integer',
        'self_weight' => 'integer',
        'attendance_weight' => 'integer',
        'activity_weight' => 'integer',
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

    public function expirePendingAssignments(): void
    {
        $this->assignments()
            ->where('status', 'pending')
            ->update(['status' => 'expired']);
    }
}
