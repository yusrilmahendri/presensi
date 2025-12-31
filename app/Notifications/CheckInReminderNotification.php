<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckInReminderNotification extends Notification
{
    use Queueable;

    public $shiftTime;

    public function __construct($shiftTime)
    {
        $this->shiftTime = $shiftTime;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Reminder: Check-in',
            'body' => 'Anda belum melakukan check-in hari ini. Shift Anda dimulai pukul ' . $this->shiftTime,
            'type' => 'check_in_reminder',
        ];
    }
}
