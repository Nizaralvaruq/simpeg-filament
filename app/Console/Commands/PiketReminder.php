<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Presensi\Models\JadwalPiket;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class PiketReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:piket-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users who have picket duty tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow();
        $this->info("Checking picket schedules for: " . $tomorrow->format('d M Y'));

        $schedules = JadwalPiket::with('user')
            ->whereDate('tanggal', $tomorrow)
            ->get();

        if ($schedules->isEmpty()) {
            $this->info("No picket schedules found for tomorrow.");
            return;
        }

        $count = 0;
        foreach ($schedules as $schedule) {
            if ($schedule->user) {
                try {
                    $schedule->user->notifyNow(new \App\Notifications\PiketBesokNotification(
                        'Pengingat Piket Besok',
                        "Assalamualaikum, Anda dijadwalkan piket besok (" . $tomorrow->format('d M Y') . ") pada shift: " . ($schedule->shift ?? '-') . ". Mohon kehadirannya.",
                        $schedule->shift,
                        $tomorrow->format('d M Y')
                    ));
                    $count++;
                } catch (\Exception $e) {
                    $this->error("Failed to notify user " . $schedule->user->id . ": " . $e->getMessage());
                }
            }
        }

        $this->info("Sent $count picket reminders.");
    }
}
