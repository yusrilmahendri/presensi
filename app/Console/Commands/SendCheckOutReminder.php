<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\CheckOutReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCheckOutReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-checkout-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send check-out reminder 30 minutes before shift ends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending check-out reminders...');
        
        $today = Carbon::today();
        $currentTime = Carbon::now();
        
        // Get all karyawan users
        $employees = User::where('role', 'karyawan')
            ->whereNotNull('shift_id')
            ->with('shift')
            ->get();
        
        $remindersSent = 0;
        
        foreach ($employees as $employee) {
            // Check if we're 30 minutes before shift end
            $shiftEnd = Carbon::parse($employee->shift->end_time);
            $reminderTime = $shiftEnd->copy()->subMinutes(30);
            
            // Calculate minutes remaining until shift ends
            $minutesRemaining = (int) $currentTime->diffInMinutes($shiftEnd, false);
            
            // Only send if current time is within 5 minutes of reminder time (30 minutes before end)
            if ($currentTime->between($reminderTime->copy()->subMinutes(2), $reminderTime->copy()->addMinutes(3))) {
                // Check if checked in today
                $hasCheckedIn = Attendance::where('user_id', $employee->id)
                    ->where('type', 'check_in')
                    ->whereDate('attendance_time', $today)
                    ->exists();
                
                if (!$hasCheckedIn) {
                    continue; // Didn't check in, no need to remind check-out
                }
                
                // Check if already checked out
                $hasCheckedOut = Attendance::where('user_id', $employee->id)
                    ->where('type', 'check_out')
                    ->whereDate('attendance_time', $today)
                    ->exists();
                
                if ($hasCheckedOut) {
                    continue; // Already checked out
                }
                
                // Send reminder
                try {
                    $employee->notify(new CheckOutReminderNotification(
                        $employee->shift->end_time,
                        $minutesRemaining
                    ));
                    
                    $remindersSent++;
                    $this->line("✓ Reminder sent to {$employee->name} ({$minutesRemaining} minutes remaining)");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send reminder to {$employee->name}: {$e->getMessage()}");
                }
            }
        }
        
        $this->info("\nTotal reminders sent: {$remindersSent}");
        return Command::SUCCESS;
    }
}
