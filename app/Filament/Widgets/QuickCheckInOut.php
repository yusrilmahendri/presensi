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
            
            // Create attendance record
            Attendance::create([
                'user_id' => $user->id,
                'shift_id' => $user->shift_id,
                'attendance_location_id' => $location->id,
                'type' => 'check_in',
                'attendance_time' => now(),
                'latitude' => 0, // Will be updated via GPS if needed
                'longitude' => 0,
                'notes' => 'Check-in melalui quick button',
            ]);
            
            $this->dispatch('check-in-success', message: 'Check-in berhasil!');
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            $this->dispatch('check-in-error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function checkOut()
    {
        try {
            $user = auth()->user();
            
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
