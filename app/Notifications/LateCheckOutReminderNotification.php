<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LateCheckOutReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $shiftEndTime;
    public $minutesLate;
    public $checkInTime;

    public function __construct($shiftEndTime, $minutesLate, $checkInTime)
    {
        $this->shiftEndTime = $shiftEndTime;
        $this->minutesLate = $minutesLate;
        $this->checkInTime = $checkInTime;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $checkInFormatted = Carbon::parse($this->checkInTime)->format('H:i');
        
        return (new MailMessage)
            ->subject('🚨 URGENT: Anda Belum Check-out!')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('⚠️ **Anda belum melakukan check-out hari ini!**')
            ->line('')
            ->line('📊 **Detail Absensi:**')
            ->line('• Check-in: **' . $checkInFormatted . '**')
            ->line('• Waktu berakhir shift: **' . $this->shiftEndTime . '**')
            ->line('• Terlambat check-out: **' . $this->minutesLate . ' menit**')
            ->line('')
            ->line('🔔 **Tindakan yang diperlukan:**')
            ->line('Jika Anda masih di area kerja:')
            ->line('• Segera lakukan check-out melalui aplikasi')
            ->line('')
            ->line('Jika Anda sudah meninggalkan area kerja:')
            ->line('• Hubungi atasan/HR untuk melakukan koreksi absensi')
            ->line('• Berikan alasan kenapa lupa check-out')
            ->line('')
            ->line('💡 **Catatan:** Lupa check-out dapat mempengaruhi perhitungan jam kerja dan kehadiran Anda.')
            ->action('Check-out Sekarang', url('/presensi'))
            ->line('Mohon segera melakukan tindakan yang diperlukan. Terima kasih!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'URGENT: Anda Belum Check-out!',
            'body' => 'Shift Anda berakhir pukul ' . $this->shiftEndTime . ' (' . $this->minutesLate . ' menit yang lalu). Anda belum melakukan check-out!',
            'type' => 'late_check_out_reminder',
            'shift_end_time' => $this->shiftEndTime,
            'minutes_late' => $this->minutesLate,
            'check_in_time' => $this->checkInTime,
            'action_required' => true,
        ];
    }
}
