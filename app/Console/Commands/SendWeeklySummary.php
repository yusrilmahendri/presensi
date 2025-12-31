<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use App\Notifications\WeeklySummaryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWeeklySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-weekly-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly attendance summary to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating weekly summary...');
        
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        // Get all admins
        $admins = User::where('role', 'admin')->get();
        
        $summariesSent = 0;
        
        foreach ($admins as $admin) {
            $organizationId = $admin->organization_id;
            
            // Get statistics for this organization
            $totalEmployees = User::where('organization_id', $organizationId)
                ->where('role', 'karyawan')
                ->count();
            
            $totalCheckIns = Attendance::whereHas('user', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->where('type', 'check_in')
                ->whereBetween('attendance_time', [$weekStart, $weekEnd])
                ->count();
            
            // Count late check-ins
            $lateCheckIns = 0;
            $attendances = Attendance::whereHas('user', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->where('type', 'check_in')
                ->whereBetween('attendance_time', [$weekStart, $weekEnd])
                ->with('user.shift')
                ->get();
            
            foreach ($attendances as $attendance) {
                if ($attendance->user->shift) {
                    $shiftStart = Carbon::parse($attendance->attendance_time->format('Y-m-d') . ' ' . $attendance->user->shift->start_time);
                    $checkInTime = Carbon::parse($attendance->attendance_time);
                    $lateThreshold = $shiftStart->copy()->addMinutes(15);
                    
                    if ($checkInTime->gt($lateThreshold)) {
                        $lateCheckIns++;
                    }
                }
            }
            
            // Count absences (employees who didn't check in on working days)
            $workingDays = $weekEnd->diffInDaysFiltered(function(Carbon $date) {
                return $date->isWeekday();
            }, $weekStart);
            
            $expectedCheckIns = $totalEmployees * $workingDays;
            $absences = $expectedCheckIns - $totalCheckIns;
            
            // Leave requests this week
            $leaveRequests = Leave::whereHas('user', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();
            
            // Send notification
            try {
                $admin->notify(new WeeklySummaryNotification([
                    'period' => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M Y'),
                    'total_employees' => $totalEmployees,
                    'total_check_ins' => $totalCheckIns,
                    'late_check_ins' => $lateCheckIns,
                    'absences' => $absences,
                    'leave_requests' => $leaveRequests,
                ]));
                
                $summariesSent++;
                $this->line("✓ Summary sent to {$admin->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send summary to {$admin->name}: {$e->getMessage()}");
            }
        }
        
        $this->info("\nTotal summaries sent: {$summariesSent}");
        return Command::SUCCESS;
    }
}
