<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckInReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $shiftTime;
    public $minutesBefore;

    public function __construct($shiftTime, $minutesBefore = 0)
    {
        $this->shiftTime = $shiftTime;
        $this->minutesBefore = $minutesBefore;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Reminder: Waktu Absen Masuk Segera Tiba')
            ->greeting('Halo ' . $notifiable->name . ',');

        if ($this->minutesBefore > 0) {
            $message->line('⏰ **Waktu absen masuk Anda akan dimulai dalam ' . $this->minutesBefore . ' menit!**')
                ->line('Shift Anda dimulai pukul ' . $this->shiftTime)
                ->line('Pastikan Anda siap untuk melakukan check-in tepat waktu.');
        } else {
            $message->line('⏰ **Anda belum melakukan check-in hari ini.**')
                ->line('Shift Anda dimulai pukul ' . $this->shiftTime)
                ->line('Segera lakukan check-in untuk menghindari keterlambatan.');
        }

        return $message
            ->line('Terima kasih atas kedisiplinan Anda!')
            ->action('Absen Sekarang', url('/presensi'));
    }

    public function toArray(object $notifiable): array
    {
        if ($this->minutesBefore > 0) {
            $body = 'Waktu absen masuk Anda akan dimulai dalam ' . $this->minutesBefore . ' menit. Shift dimulai pukul ' . $this->shiftTime;
            $title = 'Reminder: Segera Waktu Absen';
        } else {
            $body = 'Anda belum melakukan check-in hari ini. Shift Anda dimulai pukul ' . $this->shiftTime;
            $title = 'Reminder: Check-in';
        }

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'check_in_reminder',
            'shift_time' => $this->shiftTime,
            'minutes_before' => $this->minutesBefore,
        ];
    }
}
