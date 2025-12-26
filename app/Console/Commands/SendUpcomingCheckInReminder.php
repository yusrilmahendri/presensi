<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\CheckInReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendUpcomingCheckInReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-upcoming-checkin-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder to employees 5-10 minutes before their shift starts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending upcoming check-in reminders...');
        
        $today = Carbon::today();
        $currentTime = Carbon::now();
        
        // Get all karyawan users with shifts
        $employees = User::where('role', 'karyawan')
            ->whereNotNull('shift_id')
            ->with('shift')
            ->get();
        
        $remindersSent = 0;
        
        foreach ($employees as $employee) {
            if (!$employee->shift) {
                continue;
            }

            // Check if already checked in today
            $hasCheckedIn = Attendance::where('user_id', $employee->id)
                ->where('type', 'check_in')
                ->whereDate('attendance_time', $today)
                ->exists();
            
            if ($hasCheckedIn) {
                continue; // Already checked in, skip
            }

            $shiftStart = Carbon::parse($employee->shift->start_time);
            
            // Calculate time difference in minutes
            $minutesUntilShift = $currentTime->diffInMinutes($shiftStart, false);
            
            // Send reminder if between 5-10 minutes before shift
            if ($minutesUntilShift >= 5 && $minutesUntilShift <= 10) {
                try {
                    $employee->notify(new CheckInReminderNotification(
                        $employee->shift->start_time,
                        (int) $minutesUntilShift
                    ));
                    
                    $remindersSent++;
                    $this->line("✓ Reminder sent to {$employee->name} ({$minutesUntilShift} minutes before shift)");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send reminder to {$employee->name}: {$e->getMessage()}");
                }
            }
        }
        
        $this->info("\nTotal upcoming reminders sent: {$remindersSent}");
        return Command::SUCCESS;
    }
}
