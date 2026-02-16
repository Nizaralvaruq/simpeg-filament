<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PiketBesokNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $title;
    protected $shift;
    protected $tanggal;

    public function __construct($title, $message, $shift, $tanggal)
    {
        $this->title = $title;
        $this->message = $message;
        $this->shift = $shift;
        $this->tanggal = $tanggal;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'actions' => [],
            'body' => $this->message,
            'color' => 'info',
            'duration' => null,
            'icon' => 'heroicon-o-information-circle',
            'iconColor' => 'info',
            'status' => 'info',
            'title' => $this->title,
            'view' => 'filament-notifications::database-notification',
            'viewData' => [],
            'format' => 'filament',
        ];
    }
}
