<?php

namespace App\Notifications;

use App\Models\Overtime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OvertimeApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $overtime;
    public $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(Overtime $overtime, string $action = 'submitted')
    {
        $this->overtime = $overtime;
        $this->action = $action;
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
        $actionText = match($this->action) {
            'submitted' => 'Pengajuan Lembur Baru',
            'approved' => 'Lembur Disetujui',
            'rejected' => 'Lembur Ditolak',
            default => 'Update Lembur'
        };

        $message = (new MailMessage)
            ->subject($actionText)
            ->greeting('Halo ' . $notifiable->name . ',');

        if ($this->action === 'submitted') {
            $message->line($this->overtime->user->name . ' mengajukan lembur yang perlu disetujui.')
                ->line('Detail:')
                ->line('Tanggal: ' . $this->overtime->date)
                ->line('Waktu: ' . $this->overtime->start_time . ' - ' . $this->overtime->end_time)
                ->line('Durasi: ' . $this->overtime->calculateDuration() . ' jam')
                ->action('Review Sekarang', url('/admin/overtimes/' . $this->overtime->id));
        } else {
            $message->line('Lembur Anda telah ' . strtolower($actionText) . '.')
                ->line('Detail:')
                ->line('Tanggal: ' . $this->overtime->date)
                ->line('Durasi: ' . $this->overtime->calculateDuration() . ' jam');
            
            if ($this->overtime->notes) {
                $message->line('Catatan: ' . $this->overtime->notes);
            }
            
            $message->action('Lihat Detail', url('/dashboard'));
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'overtime_id' => $this->overtime->id,
            'action' => $this->action,
            'user_name' => $this->overtime->user->name,
            'date' => $this->overtime->date,
            'duration' => $this->overtime->calculateDuration(),
            'status' => $this->overtime->status,
            'message' => $this->getMessageText(),
        ];
    }

    private function getMessageText(): string
    {
        return match($this->action) {
            'submitted' => $this->overtime->user->name . ' mengajukan lembur',
            'approved' => 'Lembur Anda telah disetujui',
            'rejected' => 'Lembur Anda ditolak',
            default => 'Update lembur'
        };
