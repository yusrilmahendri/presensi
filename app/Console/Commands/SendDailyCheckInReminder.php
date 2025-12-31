<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\CheckInReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyCheckInReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-checkin-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily check-in reminder to employees who haven\'t checked in yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending check-in reminders...');
        
        $today = Carbon::today();
        $currentTime = Carbon::now();
        
        // Get all karyawan users
        $employees = User::where('role', 'karyawan')
            ->whereNotNull('shift_id')
            ->with('shift')
            ->get();
        
        $remindersSent = 0;
        
        foreach ($employees as $employee) {
            // Check if shift has started (at least 15 minutes into shift)
            $shiftStart = Carbon::parse($employee->shift->start_time);
            $reminderTime = $shiftStart->copy()->addMinutes(15);
            
            if ($currentTime->lt($reminderTime)) {
                continue; // Shift hasn't started yet
            }
            
            // Check if already checked in today
            $hasCheckedIn = Attendance::where('user_id', $employee->id)
                ->where('type', 'check_in')
                ->whereDate('attendance_time', $today)
                ->exists();
            
            if ($hasCheckedIn) {
                continue; // Already checked in
            }
            
            // Send reminder notification
            try {
                $employee->notify(new CheckInReminderNotification($employee->shift->start_time));
                
                $remindersSent++;
                $this->line("✓ Reminder sent to {$employee->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send reminder to {$employee->name}: {$e->getMessage()}");
            }
        }
        
        $this->info("\nTotal reminders sent: {$remindersSent}");
        return Command::SUCCESS;
    }
}
