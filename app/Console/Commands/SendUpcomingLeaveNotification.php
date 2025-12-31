<?php

namespace App\Console\Commands;

use App\Models\Leave;
use App\Notifications\UpcomingLeaveReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendUpcomingLeaveNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-upcoming-leave-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for leaves starting tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending upcoming leave notifications...');
        
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        // Get all approved leaves starting tomorrow
        $upcomingLeaves = Leave::where('status', 'approved')
            ->whereDate('start_date', $tomorrow)
            ->with('user')
            ->get();
        
        $notificationsSent = 0;
        
        foreach ($upcomingLeaves as $leave) {
            try {
                $leave->user->notify(new UpcomingLeaveReminderNotification($leave));
                
                $notificationsSent++;
                $this->line("✓ Notification sent to {$leave->user->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send notification to {$leave->user->name}: {$e->getMessage()}");
            }
        }
        
        $this->info("\nTotal notifications sent: {$notificationsSent}");
        return Command::SUCCESS;
    }
}
