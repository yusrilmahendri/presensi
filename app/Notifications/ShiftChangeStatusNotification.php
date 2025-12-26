<?php

namespace App\Notifications;

use App\Models\ShiftChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftChangeStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $shiftChange;
    public $status;
    public $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct($shiftChange, $status, $notes = null)
    {
        $this->shiftChange = $shiftChange;
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
            'approved' => 'Disetujui ✅',
            'rejected' => 'Ditolak ❌',
            default => 'Diperbarui'
        };

        $statusColor = match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'info'
        };

        $message = (new MailMessage)
            ->subject('Pergantian Shift ' . $statusText)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pengajuan pergantian shift Anda telah **' . strtolower($statusText) . '**.');

        if ($this->status === 'approved') {
            $message->line('🎉 Selamat! Permintaan pergantian shift Anda telah disetujui.')
                ->line('')
                ->line('**Detail Pergantian:**')
                ->line('• Shift Lama: **' . $this->shiftChange->currentShift->name . '** (' . 
                       $this->shiftChange->currentShift->start_time . ' - ' . 
                       $this->shiftChange->currentShift->end_time . ')')
                ->line('• Shift Baru: **' . $this->shiftChange->requestedShift->name . '** (' . 
                       $this->shiftChange->requestedShift->start_time . ' - ' . 
                       $this->shiftChange->requestedShift->end_time . ')')
                ->line('• Efektif Mulai: **' . $this->shiftChange->effective_date->format('d F Y') . '**')
                ->line('')
                ->line('Shift Anda akan otomatis berubah pada tanggal yang ditentukan.');
        } else {
            $message->line('😔 Maaf, permintaan pergantian shift Anda tidak dapat disetujui.')
                ->line('')
                ->line('**Detail Pengajuan:**')
                ->line('• Dari Shift: ' . $this->shiftChange->currentShift->name)
                ->line('• Ke Shift: ' . $this->shiftChange->requestedShift->name)
                ->line('• Tanggal Efektif: ' . $this->shiftChange->effective_date->format('d F Y'))
                ->line('• Alasan Anda: ' . $this->shiftChange->reason);
        }

        if ($this->notes) {
            $message->line('')
                ->line('**Catatan Admin:**')
                ->line($this->notes);
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
            'shift_change_id' => $this->shiftChange->id,
            'status' => $this->status,
            'current_shift' => $this->shiftChange->currentShift->name,
            'requested_shift' => $this->shiftChange->requestedShift->name,
            'effective_date' => $this->shiftChange->effective_date->format('Y-m-d'),
            'reason' => $this->shiftChange->reason,
            'notes' => $this->notes,
            'message' => 'Pergantian shift Anda ' . 
                        ($this->status === 'approved' ? 'disetujui' : 'ditolak'),
        ];
    }
}
