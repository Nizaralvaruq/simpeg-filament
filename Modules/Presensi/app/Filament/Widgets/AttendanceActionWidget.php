<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Widgets\Widget;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class AttendanceActionWidget extends Widget
{
    protected string $view = 'presensi::filament.widgets.attendance-action-widget';

    protected static ?int $sort = -1; // Show at the top


    protected int | string | array $columnSpan = 'full';

    public ?Absensi $todayAttendance = null;

    public function mount(): void
    {
        $this->loadAttendance();
    }

    public function loadAttendance(): void
    {
        $this->todayAttendance = Absensi::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->first();
    }

    public function checkIn(): void
    {
        if ($this->todayAttendance) {
            Notification::make()
                ->title('Anda sudah melakukan presensi hari ini.')
                ->warning()
                ->send();
            return;
        }

        Absensi::create([
            'user_id' => Auth::id(),
            'tanggal' => Carbon::today(),
            'status' => 'hadir',
            'jam_masuk' => Carbon::now()->format('H:i:s'),
        ]);

        $this->loadAttendance();

        Notification::make()
            ->title('Berhasil Check-In pada ' . Carbon::now()->format('H:i'))
            ->success()
            ->send();
    }

    public function checkOut(): void
    {
        if (!$this->todayAttendance) {
            return;
        }

        if ($this->todayAttendance->jam_keluar) {
            Notification::make()
                ->title('Anda sudah melakukan Check-Out hari ini.')
                ->warning()
                ->send();
            return;
        }

        // Rule: Only allow check-out after 12:00
        if (Carbon::now()->hour < 12) {
            Notification::make()
                ->title('Check-Out hanya diperbolehkan setelah jam 12:00.')
                ->danger()
                ->send();
            return;
        }

        $this->todayAttendance->update([
            'jam_keluar' => Carbon::now()->format('H:i:s'),
        ]);

        $this->loadAttendance();

        Notification::make()
            ->title('Berhasil Check-Out pada ' . Carbon::now()->format('H:i'))
            ->success()
            ->send();
    }
}
