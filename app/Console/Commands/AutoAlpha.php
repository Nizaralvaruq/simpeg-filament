<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoAlpha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:auto-alpha {--force}';
    protected $description = 'Set status to Alpha for employees who have not checked in by the configured time.';

    public function handle()
    {
        $settings = \Modules\MasterData\Models\Setting::get();
        $today = \Carbon\Carbon::today();

        // 1. Check if today is a working day
        if (!\Modules\Presensi\Models\Absensi::isWorkingDay($today)) {
            $this->info('Hari ini bukan hari kerja. Melewati proses Auto-Alpha.');
            return;
        }

        // 2. Check if current time is past auto_alpha_time
        $autoAlphaTime = \Carbon\Carbon::parse($settings->auto_alpha_time);
        if (\Carbon\Carbon::now()->lt($autoAlphaTime) && !$this->option('force')) {
            $this->warn('Belum mencapai waktu Auto-Alpha (' . $settings->auto_alpha_time . '). Gunakan --force untuk memaksa.');
            return;
        }

        // 3. Get all users with active employee record
        $users = \App\Models\User::whereHas('employee', function ($q) {
            $q->whereIn('status', ['Aktif', 'aktif']);
        })->get();

        $count = 0;

        foreach ($users as $user) {
            // Check if already has attendance (hadir, izin, sakit, or even already alpha)
            $exists = \Modules\Presensi\Models\Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->exists();

            if (!$exists) {
                \Modules\Presensi\Models\Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $today,
                    'status' => 'alpha',
                    'keterangan' => 'Sistem: Tidak absen sampai batas waktu ' . $settings->auto_alpha_time,
                ]);
                $count++;
            }
        }

        $this->info("Proses Auto-Alpha selesai. {$count} pegawai ditandai sebagai Alpha.");
    }
}
