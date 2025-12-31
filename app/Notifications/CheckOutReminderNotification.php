<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckOutReminderNotification extends Notification
{
    use Queueable;

    public $shiftEndTime;

    public function __construct($shiftEndTime)
    {
        $this->shiftEndTime = $shiftEndTime;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Reminder: Check-out',
            'body' => 'Shift Anda akan berakhir pukul ' . $this->shiftEndTime . '. Jangan lupa check-out.',
            'type' => 'check_out_reminder',
        ];
    }
}
