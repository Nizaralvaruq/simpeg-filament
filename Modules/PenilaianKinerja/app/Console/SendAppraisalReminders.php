<?php

namespace Modules\PenilaianKinerja\Console;

use Illuminate\Console\Command;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Notifications\AppraisalReminderNotification;
use Carbon\Carbon;

class SendAppraisalReminders extends Command
{
    protected $signature = 'appraisal:send-reminders';
    protected $description = 'Send notifications to raters who have not completed their assignments 2-3 days before the deadline';

    public function handle(): int
    {
        $this->info('Checking for appraisal deadlines...');

        // Find active sessions ending in 2 or 3 days
        $targetDates = [
            Carbon::today()->addDays(2)->toDateString(),
            Carbon::today()->addDays(3)->toDateString(),
        ];

        $sessions = AppraisalSession::where('is_active', true)
            ->whereIn('end_date', $targetDates)
            ->get();

        if ($sessions->isEmpty()) {
            $this->info('No sessions found ending in 2 or 3 days.');
            return 0;
        }

        foreach ($sessions as $session) {
            $daysRemaining = Carbon::today()->diffInDays($session->end_date, false);
            $this->info("Processing session: {$session->name} ({$daysRemaining} days remaining)");

            $pendingAssignments = AppraisalAssignment::where('session_id', $session->id)
                ->where('status', 'pending')
                ->with('rater')
                ->get();

            $notifiedCount = 0;
            foreach ($pendingAssignments as $assignment) {
                if ($assignment->rater) {
                    $assignment->rater->notify(new AppraisalReminderNotification($session, $daysRemaining));
                    $notifiedCount++;
                }
            }

            $this->info("Sent {$notifiedCount} reminders for session: {$session->name}");
        }

        $this->info('Reminder process completed.');
        return 0;
    }
}
