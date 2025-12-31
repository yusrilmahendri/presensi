<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $leave;
    public $status;
    public $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct($leave, $status, $notes = null)
    {
        $this->leave = $leave;
        $this->status = $status;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusText = match($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Diperbarui'
        };

        $message = (new MailMessage)
            ->subject('Status Cuti ' . $statusText)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Permohonan cuti Anda telah ' . strtolower($statusText) . '.')
            ->line('Detail:')
            ->line('Tanggal: ' . $this->leave->start_date->format('d/m/Y') . ' - ' . $this->leave->end_date->format('d/m/Y'))
            ->line('Jumlah Hari: ' . $this->leave->days . ' hari')
            ->line('Alasan: ' . $this->leave->reason);

        if ($this->notes) {
            $message->line('Catatan: ' . $this->notes);
        }

        return $message->action('Lihat Detail', url('/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_id' => $this->leave->id,
            'status' => $this->status,
            'start_date' => $this->leave->start_date->format('Y-m-d'),
            'end_date' => $this->leave->end_date->format('Y-m-d'),
            'days' => $this->leave->days,
            'reason' => $this->leave->reason,
            'notes' => $this->notes,
            'message' => 'Permohonan cuti Anda telah ' . $this->status,
        ];
    }
}
