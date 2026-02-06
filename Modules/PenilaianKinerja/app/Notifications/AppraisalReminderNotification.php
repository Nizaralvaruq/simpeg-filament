<?php

namespace Modules\PenilaianKinerja\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\PenilaianKinerja\Models\AppraisalSession;

class AppraisalReminderNotification extends Notification
{
    use Queueable;

    protected $session;
    protected $daysRemaining;

    public function __construct(AppraisalSession $session, int $daysRemaining)
    {
        $this->session = $session;
        $this->daysRemaining = $daysRemaining;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengingat: Penilaian Kinerja Belum Selesai')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Ini adalah pengingat bahwa Anda memiliki tugas penilaian kinerja yang belum diselesaikan untuk sesi: **' . $this->session->name . '**.')
            ->line('Sesi ini akan berakhir dalam **' . $this->daysRemaining . ' hari** (' . $this->session->end_date->format('d F Y') . ').')
            ->action('Mulai Penilaian', url('/admin/penilaian-kinerja/tugas-penilaian-saya'))
            ->line('Mohon segera dilengkapi sebelum batas waktu berakhir.')
            ->line('Terima kasih atas kerja samanya.');
    }

    public function toArray($notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title('Pengingat Penilaian Kinerja')
            ->body('Segera selesaikan tugas penilaian untuk sesi ' . $this->session->name . '. Sisa waktu ' . $this->daysRemaining . ' hari.')
            ->icon('heroicon-o-calendar')
            ->color('warning')
            ->getDatabaseMessage();
    }
}
