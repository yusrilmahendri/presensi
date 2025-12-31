<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class LateCheckInNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $attendance;
    public $lateMinutes;

    /**
     * Create a new notification instance.
     */
    public function __construct($attendance, $lateMinutes)
    {
        $this->attendance = $attendance;
        $this->lateMinutes = $lateMinutes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Only database, no email spam
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Keterlambatan Absen')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Anda terlambat melakukan check-in hari ini.')
            ->line('Waktu check-in: ' . $this->attendance->attendance_time->format('H:i'))
            ->line('Terlambat: ' . $this->lateMinutes . ' menit')
            ->line('Harap lebih tepat waktu di kemudian hari.')
            ->action('Lihat Presensi', url('/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'attendance_id' => $this->attendance->id,
            'late_minutes' => $this->lateMinutes,
            'check_in_time' => $this->attendance->attendance_time->format('H:i'),
            'date' => $this->attendance->attendance_time->format('Y-m-d'),
            'message' => 'Anda terlambat check-in ' . $this->lateMinutes . ' menit',
        ];
    }
}