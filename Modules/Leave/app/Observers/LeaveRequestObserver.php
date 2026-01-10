<?php

namespace Modules\Leave\Observers;

use Modules\Leave\Models\LeaveRequest;
use Modules\Presensi\Models\Absensi;
use Carbon\CarbonPeriod;

class LeaveRequestObserver
{
    /**
     * Handle the LeaveRequest "updated" event.
     */
    public function updated(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->wasChanged('status') && $leaveRequest->status === 'approved') {
            $this->syncToAttendance($leaveRequest);
        }
    }

    /**
     * Sync approved leave to attendance records.
     */
    protected function syncToAttendance(LeaveRequest $leaveRequest): void
    {
        $employee = $leaveRequest->employee;
        if (!$employee || !$employee->user_id) {
            return;
        }

        $period = CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date);

        foreach ($period as $date) {
            Absensi::updateOrCreate(
                [
                    'user_id' => $employee->user_id,
                    'tanggal' => $date->format('Y-m-d'),
                ],
                [
                    'status' => 'izin', // Or 'cuti' if you add it to the status options
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                    'keterangan' => 'Cuti: ' . $leaveRequest->reason,
                ]
            );
        }
    }
}
