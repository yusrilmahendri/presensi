<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Pages\Page;

class AttendanceCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.attendance-calendar';
    
    protected static ?string $navigationLabel = 'Kalender Kehadiran';
    
    protected static ?string $navigationGroup = 'Absensi';
    
    protected static ?int $navigationSort = 2;
    
    public $year;
    public $month;
    public $attendanceData = [];
    
    public static function canAccess(): bool
    {
        return auth()->user()->role !== 'super_admin';
    }
    
    public function mount(): void
    {
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
        $this->loadAttendanceData();
    }
    
    public function loadAttendanceData(): void
    {
        $user = auth()->user();
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        if ($user->role === 'admin') {
            // Admin: lihat semua karyawan di organisasinya
            $attendances = Attendance::where('type', 'check_in')
                ->whereBetween('attendance_time', [$startDate, $endDate])
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('organization_id', $user->organization_id)
                        ->where('role', 'karyawan');
                })
                ->with('user')
                ->get()
                ->groupBy(function ($item) {
                    return $item->attendance_time->format('Y-m-d');
                });
        } else {
            // Karyawan: lihat attendance sendiri
            $attendances = Attendance::where('user_id', $user->id)
                ->where('type', 'check_in')
                ->whereBetween('attendance_time', [$startDate, $endDate])
                ->get()
                ->groupBy(function ($item) {
                    return $item->attendance_time->format('Y-m-d');
                });
        }
        
        $this->attendanceData = [];
        
        for ($day = 1; $day <= $endDate->day; $day++) {
            $date = Carbon::create($this->year, $this->month, $day);
            $dateStr = $date->format('Y-m-d');
            
            $status = 'alpha';
            $count = 0;
            $color = 'bg-red-100 text-red-800';
            $tooltip = 'Alpha';
            
            if ($date->isFuture()) {
                $status = 'future';
                $color = 'bg-gray-50 text-gray-400';
                $tooltip = 'Belum terjadi';
            } elseif ($date->isWeekend()) {
                $status = 'weekend';
                $color = 'bg-purple-100 text-purple-800';
                $tooltip = 'Weekend';
            } elseif (isset($attendances[$dateStr])) {
                $dayAttendances = $attendances[$dateStr];
                $count = $dayAttendances->count();
                
                if ($user->role === 'admin') {
                    $status = 'present';
                    $color = 'bg-green-100 text-green-800';
                    $tooltip = $count . ' karyawan hadir';
                } else {
                    $attendance = $dayAttendances->first();
                    if ($user->shift) {
                        $shiftStart = Carbon::parse($date->format('Y-m-d') . ' ' . $user->shift->start_time);
                        $checkIn = Carbon::parse($attendance->attendance_time);
                        $lateThreshold = $shiftStart->copy()->addMinutes(15);
                        
                        if ($checkIn->gt($lateThreshold)) {
                            $status = 'late';
                            $color = 'bg-yellow-100 text-yellow-800';
                            $tooltip = 'Terlambat - ' . $checkIn->format('H:i');
                        } else {
                            $status = 'ontime';
                            $color = 'bg-green-100 text-green-800';
                            $tooltip = 'Tepat waktu - ' . $checkIn->format('H:i');
                        }
                    } else {
                        $status = 'present';
                        $color = 'bg-green-100 text-green-800';
                        $tooltip = 'Hadir - ' . $attendance->attendance_time->format('H:i');
                    }
                }
            }
            
            $this->attendanceData[] = [
                'date' => $day,
                'full_date' => $dateStr,
                'status' => $status,
                'color' => $color,
                'count' => $count,
                'tooltip' => $tooltip,
                'is_today' => $date->isToday(),
            ];
        }
    }
    
    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->loadAttendanceData();
    }
    
    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->loadAttendanceData();
    }
    
    public function today(): void
    {
        $this->year = Carbon::now()->year;
        $this->month = Carbon::now()->month;
        $this->loadAttendanceData();
    }
}
