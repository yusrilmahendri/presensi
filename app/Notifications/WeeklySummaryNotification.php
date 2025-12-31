<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklySummaryNotification extends Notification
{
    use Queueable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Ringkasan Mingguan',
            'body' => "Total Karyawan: {$this->data['total_employees']} | Check-in: {$this->data['total_check_ins']} | Terlambat: {$this->data['late_check_ins']} | Alpha: {$this->data['absences']} | Izin/Cuti: {$this->data['leave_requests']}",
            'type' => 'weekly_summary',
            'data' => $this->data,
        ];
    }
}
