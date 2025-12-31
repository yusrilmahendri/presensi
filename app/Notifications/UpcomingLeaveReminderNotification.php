<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingLeaveReminderNotification extends Notification
{
    use Queueable;

    public $leave;

    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengingat: Cuti Dimulai Besok',
            'body' => "Cuti Anda ({$this->leave->type}) akan dimulai besok, " . \Carbon\Carbon::parse($this->leave->start_date)->format('d M Y') . " hingga " . \Carbon\Carbon::parse($this->leave->end_date)->format('d M Y') . " ({$this->leave->days} hari).",
            'type' => 'upcoming_leave',
            'data' => [
                'leave_id' => $this->leave->id,
                'type' => $this->leave->type,
                'start_date' => $this->leave->start_date,
                'end_date' => $this->leave->end_date,
                'days' => $this->leave->days,
            ],
        ];
    }
}
