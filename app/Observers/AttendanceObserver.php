<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\Overtime;
use App\Notifications\LateCheckInNotification;
use Carbon\Carbon;

class AttendanceObserver
{
    /**
     * Handle the Attendance "created" event.
     */
    public function created(Attendance $attendance): void
    {
        // Check for late check-in
        if ($attendance->type === 'check_in') {
            $this->checkLateCheckIn($attendance);
        }
        
        // Auto-detect overtime on check-out
        if ($attendance->type === 'check_out') {
            $this->detectOvertime($attendance);
        }
    }

    /**
     * Check if employee is late and notify them
     */
    private function checkLateCheckIn(Attendance $attendance): void
    {
        if (!$attendance->user->shift) {
            return;
        }

        $shiftStart = Carbon::parse($attendance->user->shift->start_time);
        $checkInTime = Carbon::parse($attendance->attendance_time);
        
        // Add 15 minutes grace period
        $lateThreshold = $shiftStart->copy()->addMinutes(15);
        
        if ($checkInTime->gt($lateThreshold)) {
            $lateMinutes = $checkInTime->diffInMinutes($shiftStart);
            
            // Send notification to employee
            $attendance->user->notify(new LateCheckInNotification($attendance, $lateMinutes));
        }
    }

    /**
     * Auto-detect overtime from late check-out
     */
    private function detectOvertime(Attendance $attendance): void
    {
        if (!$attendance->user->shift) {
            return;
        }

        // Find corresponding check-in
        $checkIn = Attendance::where('user_id', $attendance->user_id)
            ->where('type', 'check_in')
            ->whereDate('attendance_time', $attendance->attendance_time->toDateString())
            ->orderBy('attendance_time', 'desc')
            ->first();

        if (!$checkIn) {
            return;
        }

        $shiftEnd = Carbon::parse($checkIn->attendance_time->toDateString() . ' ' . $attendance->user->shift->end_time);
        $actualEnd = Carbon::parse($attendance->attendance_time);
        
        // If checked out after shift end time
        if ($actualEnd->gt($shiftEnd)) {
            $overtimeMinutes = $actualEnd->diffInMinutes($shiftEnd);
            
            // Minimum 30 minutes to count as overtime
            if ($overtimeMinutes >= 30) {
                Overtime::create([
                    'user_id' => $attendance->user_id,
                    'organization_id' => $attendance->user->organization_id,
                    'attendance_id' => $attendance->id,
                    'date' => $attendance->attendance_time->toDateString(),
                    'start_time' => $shiftEnd->format('H:i:s'),
                    'end_time' => $actualEnd->format('H:i:s'),
                    'duration_minutes' => $overtimeMinutes,
                    'multiplier' => $this->getOvertimeMultiplier($actualEnd),
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Get overtime multiplier based on time
     */
    private function getOvertimeMultiplier(Carbon $time): float
    {
        $hour = $time->hour;
        
        // Weekend
        if ($time->isWeekend()) {
            return 2.0;
        }
        
        // Night shift (22:00 - 06:00)
        if ($hour >= 22 || $hour < 6) {
            return 1.75;
        }
        
        // Regular overtime
        return 1.5;
    }
}
