<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\LateCheckOutReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLateCheckOutReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-late-checkout-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder to employees who forgot to check-out after shift ends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for employees who forgot to check-out...');
        
        $today = Carbon::today();
        $currentTime = Carbon::now();
        
        // Get all karyawan users
        $employees = User::where('role', 'karyawan')
            ->whereNotNull('shift_id')
            ->with('shift')
            ->get();
        
        $remindersSent = 0;
        
        foreach ($employees as $employee) {
            // Check if shift has ended
            $shiftEnd = Carbon::parse($employee->shift->end_time);
            
            // Only check if shift already ended (at least 15 minutes past)
            if ($currentTime->lessThan($shiftEnd->copy()->addMinutes(15))) {
                continue;
            }
            
            // Check if checked in today
            $checkIn = Attendance::where('user_id', $employee->id)
                ->where('type', 'check_in')
                ->whereDate('attendance_time', $today)
                ->first();
            
            if (!$checkIn) {
                continue; // Didn't check in, no need to remind check-out
            }
            
            // Check if already checked out
            $checkOut = Attendance::where('user_id', $employee->id)
                ->where('type', 'check_out')
                ->whereDate('attendance_time', $today)
                ->first();
            
            if ($checkOut) {
                continue; // Already checked out
            }
            
            // Calculate how late (minutes past shift end)
            $minutesLate = (int) $shiftEnd->diffInMinutes($currentTime);
            
            // Only send reminder if:
            // 1. At least 15 minutes past shift end
            // 2. Not more than 2 hours past (assume they left already)
            if ($minutesLate >= 15 && $minutesLate <= 120) {
                // Check if reminder already sent in last hour (avoid spam)
                $lastReminder = \DB::table('notifications')
                    ->where('notifiable_id', $employee->id)
                    ->where('type', 'App\Notifications\LateCheckOutReminderNotification')
                    ->where('created_at', '>', Carbon::now()->subHour())
                    ->first();
                
                if ($lastReminder) {
                    $this->line("⏭ Skipped {$employee->name} (reminder already sent)");
                    continue;
                }
                
                // Send reminder
                try {
                    $employee->notify(new LateCheckOutReminderNotification(
                        $employee->shift->end_time,
                        $minutesLate,
                        $checkIn->attendance_time
                    ));
                    
                    $remindersSent++;
                    $this->line("✓ Reminder sent to {$employee->name} ({$minutesLate} minutes late)");
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send reminder to {$employee->name}: {$e->getMessage()}");
                }
            }
        }
        
        $this->info("\nTotal late check-out reminders sent: {$remindersSent}");
        return Command::SUCCESS;
    }
}
