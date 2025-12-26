<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class QuickCheckInOut extends Widget
{
    protected static string $view = 'filament.widgets.quick-check-in-out';
    
    protected static ?int $sort = 2;
    
    public static function canView(): bool
    {
        return auth()->user()->role === 'karyawan';
    }
    
    public function checkIn()
    {
        try {
            $user = auth()->user();
            
            // Check if user is on approved leave today
            $today = Carbon::today();
            $hasLeaveToday = \App\Models\Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->exists();
            
            if ($hasLeaveToday) {
                $this->dispatch('check-in-error', message: 'Anda tidak dapat melakukan check-in karena sedang dalam masa cuti/izin yang telah disetujui.');
                return;
            }
            
            // Check if already checked in today
            $existingCheckIn = Attendance::where('user_id', $user->id)
                ->where('type', 'check_in')
                ->whereDate('attendance_time', Carbon::today())
                ->first();
            
            if ($existingCheckIn) {
                $this->dispatch('check-in-error', message: 'Anda sudah melakukan check-in hari ini!');
                return;
            }
            
            // Get default location (first location)
            $location = AttendanceLocation::first();
            
            if (!$location) {
                $this->dispatch('check-in-error', message: 'Lokasi absensi tidak ditemukan!');
                return;
            }
            
            // Determine attendance status
            $status = null;
            $organization = $user->organization;
            
            // Mode Working Hours: Karyawan bebas check-in kapan saja
            if ($organization && $organization->isWorkingHoursBased()) {
                $status = 'flexible';
            }
            // Mode Shift: Validasi berdasarkan jam shift
            elseif ($user->shift) {
                $currentTime = Carbon::now('Asia/Jakarta');
                $shiftStart = Carbon::parse($user->shift->start_time);
                $shiftStart->setDate($currentTime->year, $currentTime->month, $currentTime->day);
                
                // Calculate difference in minutes (negative = early, positive = late)
                $diffMinutes = $currentTime->diffInMinutes($shiftStart, false);
                
                if ($diffMinutes < -15) {
                    $status = 'early';
                } elseif ($diffMinutes <= 0) {
                    $status = 'on_time';
                } else {
                    $status = 'late';
                }
            }
            
            // Create attendance record
            Attendance::create([
                'user_id' => $user->id,
                'shift_id' => $user->shift_id,
                'attendance_location_id' => $location->id,
                'type' => 'check_in',
                'status' => $status,
                'attendance_time' => now(),
                'latitude' => 0, // Will be updated via GPS if needed
                'longitude' => 0,
                'notes' => 'Check-in melalui quick button',
            ]);
            
            $statusMessage = '';
            if ($status === 'late') {
                $statusMessage = ' Anda terlambat.';
            } elseif ($status === 'early') {
                $statusMessage = ' Anda lebih awal.';
            } else {
                $statusMessage = ' Tepat waktu!';
            }
            
            $this->dispatch('check-in-success', message: 'Check-in berhasil!' . $statusMessage);
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            $this->dispatch('check-in-error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function checkOut()
    {
        try {
            $user = auth()->user();
            
            // Check if user is on approved leave today
            $today = Carbon::today();
            $hasLeaveToday = \App\Models\Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->exists();
            
            if ($hasLeaveToday) {
                $this->dispatch('check-out-error', message: 'Anda tidak dapat melakukan check-out karena sedang dalam masa cuti/izin yang telah disetujui.');
                return;
            }
            
            // Check if already checked out today
            $existingCheckOut = Attendance::where('user_id', $user->id)
                ->where('type', 'check_out')
                ->whereDate('attendance_time', Carbon::today())
                ->first();
            
            if ($existingCheckOut) {
                $this->dispatch('check-out-error', message: 'Anda sudah melakukan check-out hari ini!');
                return;
            }
            
            // Check if checked in today
            $checkIn = Attendance::where('user_id', $user->id)
                ->where('type', 'check_in')
                ->whereDate('attendance_time', Carbon::today())
                ->first();
            
            if (!$checkIn) {
                $this->dispatch('check-out-error', message: 'Anda belum melakukan check-in hari ini!');
                return;
            }

            // Mode Working Hours: Validasi jam kerja minimum
            $organization = $user->organization;
            if ($organization && $organization->isWorkingHoursBased()) {
                $checkInTime = Carbon::parse($checkIn->attendance_time);
                $currentTime = Carbon::now('Asia/Jakarta');
                $hoursWorked = $checkInTime->diffInHours($currentTime, true);
                $minHours = $organization->min_working_hours;

                if ($hoursWorked < $minHours) {
                    $remainingHours = $minHours - $hoursWorked;
                    $remainingMinutes = round(($remainingHours - floor($remainingHours)) * 60);
                    
                    $message = "⏰ Belum mencapai jam kerja minimum!\n\n" .
                              "📋 Jam kerja minimum: {$minHours} jam\n" .
                              "⏱️ Anda telah bekerja: " . floor($hoursWorked) . " jam " . round(($hoursWorked - floor($hoursWorked)) * 60) . " menit\n" .
                              "⚠️ Kurang: " . floor($remainingHours) . " jam " . $remainingMinutes . " menit lagi";
                    
                    $this->dispatch('check-out-error', message: $message);
                    return;
                }
            }
            
            // Get default location
            $location = AttendanceLocation::first();
            
            if (!$location) {
                $this->dispatch('check-out-error', message: 'Lokasi absensi tidak ditemukan!');
                return;
            }
            
            // Create checkout record
            Attendance::create([
                'user_id' => $user->id,
                'shift_id' => $user->shift_id,
                'attendance_location_id' => $location->id,
                'type' => 'check_out',
                'attendance_time' => now(),
                'latitude' => 0,
                'longitude' => 0,
                'notes' => 'Check-out melalui quick button',
            ]);
            
            $this->dispatch('check-out-success', message: 'Check-out berhasil!');
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            $this->dispatch('check-out-error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function getCheckInToday()
    {
        return Attendance::where('user_id', auth()->id())
            ->where('type', 'check_in')
            ->whereDate('attendance_time', Carbon::today())
            ->first();
    }
    
    public function getCheckOutToday()
    {
        return Attendance::where('user_id', auth()->id())
            ->where('type', 'check_out')
            ->whereDate('attendance_time', Carbon::today())
            ->first();
    }
}
