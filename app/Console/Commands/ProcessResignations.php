<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Resign\Models\Resign;
use Modules\Kepegawaian\Models\DataInduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessResignations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-resignations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengeksekusi penonaktifan akun untuk pengajuan resign yang jatuh tempo hari ini atau sebelumnya';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $this->info("Starting resignation processing for date <= {$today}");

        // Find approved resignations where resignation date is today or past,
        // and the employee's status is still NOT 'Resign'
        $dueResignations = Resign::with(['employee.user'])
            ->where('status', 'disetujui')
            ->whereDate('tanggal_resign', '<=', $today)
            ->whereHas('employee', function ($query) {
                $query->where('status', '!=', 'Resign');
            })
            ->get();

        $processedCount = 0;

        foreach ($dueResignations as $resign) {
            $employee = $resign->employee;

            if ($employee) {
                // Update Data Induk status
                $employee->update([
                    'status' => 'Resign',
                    'keterangan' => $resign->alasan,
                ]);

                // Delete User account if exists
                if ($employee->user) {
                    $employee->user->delete();
                }

                $processedCount++;
                Log::info("Processed resignation for employee ID: {$employee->id} ({$employee->nama}). Date: {$resign->tanggal_resign}");
            }
        }

        $this->info("Successfully processed {$processedCount} resignations.");
    }
}
