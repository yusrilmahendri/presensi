<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckOutReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $shiftEndTime;
    public $minutesRemaining;

    public function __construct($shiftEndTime, $minutesRemaining = 30)
    {
        $this->shiftEndTime = $shiftEndTime;
        $this->minutesRemaining = $minutesRemaining;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⏰ Reminder: Waktu Check-out Segera Tiba')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('🕐 **Shift Anda akan berakhir dalam ' . $this->minutesRemaining . ' menit!**')
            ->line('Waktu berakhir shift: **' . $this->shiftEndTime . '**')
            ->line('')
            ->line('📝 **Jangan lupa untuk:**')
            ->line('• Menyelesaikan pekerjaan yang sedang berjalan')
            ->line('• Melakukan check-out sebelum meninggalkan area kerja')
            ->line('• Pastikan semua tugas sudah terdokumentasi')
            ->line('')
            ->line('Terima kasih atas kerja keras Anda hari ini! 💪')
            ->action('Check-out Sekarang', url('/presensi'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Reminder: Waktu Check-out Segera Tiba',
            'body' => 'Shift Anda akan berakhir dalam ' . $this->minutesRemaining . ' menit (pukul ' . $this->shiftEndTime . '). Jangan lupa check-out!',
            'type' => 'check_out_reminder',
            'shift_end_time' => $this->shiftEndTime,
            'minutes_remaining' => $this->minutesRemaining,
        ];
    }
}
